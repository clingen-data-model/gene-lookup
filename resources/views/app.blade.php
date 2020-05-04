<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=1280, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>OMIM Phenotypes for gene symbol</title>

        <script src="{{mix('/js/app.js')}}"></script>
        <link rel="stylesheet" href="{{mix('/css/app.css')}}">
    </head>
    <body>
        <div class="mx-auto container p-5 border-4 bg-white">
            <h1 class="border-b pb-2 mb-4">OMIM Lookup by gene</h1>
            <div class="grid grid-cols-5 gap-4">
                <div class="col-span-1">
                    <form action="/" method="POST" class="col-span-1 border" enctype='multipart/form-data'>
                        {{csrf_field()}}
                        <label for="file-upload" class="align-top block">From CSV:</label>
                        <input type="file" id="file-upload" name="gene_symbol_file" value="{{$gene_symbol_file}}">
                        <div><input type="checkbox" id="has-header" name="has_header" @if (isset($has_header)) checked @endif><label for="has-header">File has header</label></div>
                        <button 
                            type="submit" 
                            class="bg-gray-200 rounded-md p-2 my-1 hover:bg-gray-300 active:bg-gray-400 submit-button"
                        >
                            Submit
                        </button>
                        <button 
                            type="submit" 
                            class="bg-gray-200 rounded-md p-2 my-1 hover:bg-gray-300 active:bg-gray-400 csv-submit-button"
                        >
                            Get CSV
                        </button>
                    </form>
                    <small>or</small>
                    <form action="/" method="POST" class="col-span-1 border" id="manual-form">
                        {{csrf_field()}}
                        <label class=" align-top block">Gene symbols <small><small>(comma separated)</small></small></label>
                        <textarea name="gene_symbols" id="gene-symbols-input" class="block border-2" cols="25" rows="10">{{ $geneSymbols->implode(",\n") }}</textarea>
                        <button 
                            type="submit" 
                            class="bg-gray-200 rounded-md p-2 my-1 hover:bg-gray-300 active:bg-gray-400 submit-button"
                            >Submit</button>
                        <button 
                            type="submit" 
                            class="bg-gray-200 rounded-md p-2 my-1 hover:bg-gray-300 active:bg-gray-400 csv-submit-button"
                        >
                            Get CSV
                        </button>
                    </form>
                </div>
                <div class='col-span-4'>
                    @if($results->count() > 0)
                        <form action="/csv" method="POST">
                            {{csrf_field()}}
                            <input type="hidden" name="gene_symbols" value="{{ $geneSymbols->implode(",\n") }}">
                            <button class="bg-gray-200 rounded-md p-2 my-1 hover:bg-gray-300 active:bg-gray-400" type="submit">Download CSV</button>
                        </form>
                        <table class="border border-collapse table-fixed">
                            <tr>
                                <th class="border p-1">Gene symbol</th>
                                <th class="border p-1">Phenotype</th>
                                <th class="border p-1">MOI</th>
                                <th class="border p-1">MIM number</th>
                            </tr>
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
                                            <td class="border p-1">{{$result->matches}}</td>
                                            <td class="border p-1" colspan="3">This gene does not have any phenotypes in OMIM</td>
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
                        </table>
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
                        // parentForm.setAttribute('action', '/');
                    })
                })
            });
        </script>
    </body>
</html>