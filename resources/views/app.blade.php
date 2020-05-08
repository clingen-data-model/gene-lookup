@extends('layouts.app')

@section('content')
        <div class="mx-auto container p-5 border-4 bg-white">
            <h1 class="border-b pb-2 mb-4">OMIM Lookup by gene</h1>
            <div class="row">
                <div class="col-sm-3">
                    <form action="/" method="POST" class="col-span-1" enctype='multipart/form-data'>
                        {{csrf_field()}}
                        <label for="file-upload" class="align-top block">From CSV:</label>
                        <input type="file" id="file-upload" name="gene_symbol_file" value="{{$gene_symbol_file}}">
                        <div class="mt-1">
                            <input type="checkbox" id="has-header" name="has_header" @if (isset($has_header)) checked @endif>
                            <label for="has-header">File has header</label>
                        </div>
                        <button 
                            type="submit" 
                            class="btn btn-sm btn-primary"
                        >
                            Submit
                        </button>
                        {{-- <button 
                            type="button" 
                            class="bg-gray-200 rounded-md p-2 my-1 hover:bg-gray-300 active:bg-gray-400 csv-submit-button"
                        >
                            Get CSV
                        </button> --}}
                    </form>
                    <small>or</small>
                    <form action="/" method="POST" class="col-span-1" id="manual-form">
                        {{csrf_field()}}
                        <label class=" align-top block">Gene symbols <small><small>(comma separated)</small></small></label>
                        <textarea name="gene_symbols" id="gene-symbols-input" class="block border-2" cols="25" rows="10">{{ $geneSymbols->implode(",\n") }}</textarea>
                        <button type="submit" 
                            class="btn btn-sm btn-primary"
                        >
                            Submit
                        </button>
                        {{-- <button type="button" 
                            class="bg-gray-200 rounded-md p-2 my-1 hover:bg-gray-300 active:bg-gray-400 csv-submit-button"
                        >
                            Get CSV
                        </button> --}}
                    </form>
                </div>
                <div class='col-sm-9'>
                    @if($results->count() == 0) 
                        <div class="alert alert-secondary w-75">
                            <h4>How to use this tool:</h4>
                            <ol>
                                <li>Upload a csv with a list of gene symbols in the first column OR enter a comma separated list of gene symbols by hand in the text area.</li>
                                <li>Click submit on the appropriate form.</li>
                                <li>If you would like to download a csv of the OMIM phenotype information, click the <button class="btn btn-light border btn-sm" disabled>Download CSV</button> button</li>
                            </ol>
                        </div>
                        <div class="alert alert-warning w-75">We currently only get 5000 requests to the OMIM API each day. Please look for a small number of genes until we work out our daily OMIM API request cap. <br> Thanks.</div>
                    @endif
                    @if($results->count() > 0)
                        <div class="clearfix mb-2">
                            <form action="/csv" method="POST" id="csv-form" class="float-right text-right">
                                {{csrf_field()}}
                                <input type="hidden" name="gene_symbols" value="{{ $geneSymbols->implode(",\n") }}">
                                <button class="btn btn-sm btn-light border" type="submit">Download CSV</button>
                            </form>
                            Found {{$phenotypeCount}} phenotypes for {{$results->count()}} genes.
                        </div>
                        <div class="border-bottom border-top" style="max-height: 600px; overflow-y: scroll;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="border p-1">Gene symbol</th>
                                        <th class="border p-1">Phenotype</th>
                                        <th class="border p-1">MOI</th>
                                        <th class="border p-1">MIM number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $result)
                                        @php try { @endphp
                                            @if (isset($result->geneMap->phenotypeMapList))
                                                @foreach ($result->geneMap->phenotypeMapList as $items)
                                                    @php $phenoMap = $items->phenotypeMap @endphp
                                                    <tr>
                                                        <td class="border p-1">{{$result->matches}}</td>
                                                        <td class="border p-1">{{$phenoMap->phenotype ?? '?'}}</td>
                                                        <td class="border p-1">{{$phenoMap->phenotypeInheritance}}</td>
                                                        <td class="border p-1">{{$phenoMap->phenotypeMimNumber ?? '?'}}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="bg-orange-300">
                                                    <td class="border p-1 text-muted">{{$result->matches}}</td>
                                                    <td class="border p-1 text-muted" colspan="3">This gene does not have any phenotypes in OMIM</td>
                                                </tr>
                                            @endif
                                        @php } catch (\Exception $e) { @endphp
                                            <tr class="bg-orange-300">
                                                <td class="border p-1">{{$result->matches}}</td>
                                                <td colspan=3 class="bg-black text-white">
                                                    <pre>{{$e->getMessage()}}</pre>
                                                    @php dump($result) @endphp
                                                </td>
                                            </tr>
                                        @php } @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-warning mt-2">
                            🔥 Data from OMIM may be up to 24 hours old.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const submitButtons = Array.from(document.getElementsByClassName('csv-submit-button'));
                submitButtons.forEach(function (button) {
                    button.addEventListener('click', function (evt) {
                        console.log(evt.target.parentElement);
                        const parentForm = evt.target.parentElement
                        evt.preventDefault();
                        parentForm.setAttribute('action', '/csv');
                        parentForm.submit();
                    })
                })
            });
        </script>
@endsection