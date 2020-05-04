<?php

namespace App\Http\Controllers;

use App\Omim\OmimClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class OmimLookupController extends Controller
{
    protected $omimClient;

    public function __construct(OmimClient $omimClient)
    {
        $this->omimClient = $omimClient;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app', ['geneSymbols' => collect(), 'results' => collect(), 'has_header' => null, 'gene_symbol_file' => null]);
    }

    public function query(Request $request)
    {
        $geneSymbols = $this->getSymbolsFromRequest($request);
        $results = $this->getOmimResults($geneSymbols);
        $has_header = $request->has_header;
        $gene_symbol_file = $request->gene_symbol_file;
        return view('app', compact('results', 'geneSymbols', 'has_header', 'gene_symbol_file'));
    }

    public function getCsv(Request $request)
    {
        $geneSymbols = $this->getSymbolsFromRequest($request);
        $results = $this->getOmimResults($geneSymbols);
        
        $filepath = '/tmp/'.uniqid().'.csv';
        $fh = fopen($filepath, 'w');
        fputcsv($fh, ['Gene symbol', 'Phenotype', 'MOI', 'Phenotype mimNumber']);

        $results->each(function ($entry) use ($fh) {
            if (isset($entry->geneMap) && isset($entry->geneMap->phenotypeMapList)) {
                foreach ($entry->geneMap->phenotypeMapList as $item) {
                    $phenoMap = $item->phenotypeMap;
                    fputcsv($fh, [
                        'gene_symbol' => $entry->matches,
                        'phenotype' => $phenoMap->phenotype ?? '',
                        'MOI' => $phenoMap->phenotypeInheritance ?? '',
                        'phenotype mimNumber' => $phenoMap->phenotypeMimNumber ?? '',
                    ]);
                }
            }
        });
        fclose($fh);

        return response()->download($filepath)->deleteFileAfterSend();
    }

    private function getOmimResults($geneSymbols)
    {
        $results = collect();

        $chunks = 0;
        $geneSymbols->chunk(20)->each(function ($chunk) use ($results, $chunks) {
            $search = $chunk->map(function ($symbol) {
                return 'approved_gene_symbol:'.$symbol;
            })->implode(' OR ');
            try {
                $entries = $this->omimClient->search(['search' => $search, 'include'=>'geneMap', 'limit'=>20]);
                $results = $results->push($entries);
            } catch (ClientException $e) {
                if ($e->getCode() == 429) {
                    sleep(2);
                    try {
                        $entries = $this->omimClient->search(['search' => $search, 'include'=>'geneMap', 'limit'=>20]);
                        $results = $results->push($entries);
                    } catch (ClientException $ce) {
                        // if ($ce->getCode() == 429) {
                        //     return false;
                        // }
                        throw $ce;
                    }
                }
                throw $e;
            }
            if ($chunks > 4) {
                return false;
            }
            $chunks++;
        });

        $results = $results->flatten();
        return $results;
    }
    

    private function getSymbolsFromRequest(Request $request)
    {
        if ($request->hasFile('gene_symbol_file')) {
            $file = $request->gene_symbol_file;
            $fh = fopen($file->getRealPath(), 'r');

            $symbols = collect();
            $rowCount = 0;
            while (($data = fgetcsv($fh, 1000, ",")) !== false) {
                if ($request->file_has_header && $rowCount == 0) {
                    continue;
                }
                $rowCount++;
                $symbols->push($data[0]);
            }

            fclose($fh);

            return $symbols;
        }

        if ($request->gene_symbols) {
            return collect(explode(',', $request->gene_symbols))->trim()->filter();
        }

        return collect();
    }
}
