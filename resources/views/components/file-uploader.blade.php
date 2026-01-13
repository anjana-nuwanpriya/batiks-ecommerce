@props([
    'pondName' => '',
    'pondID' => '',
    'pondCollection' => '',
    'pondInstanceName' => '',
    'pondLable' => '',
    'pondMedia' => '',
    'inputLabel' => '',
])
<label class="form-label">{{$inputLabel}}</label>
<input type="file" {{ $attributes->has('multiple') ? $attributes->merge(['class' => 'multi-fiepond']) : $attributes }} name="{{$pondName}}" id="{{$pondID}}">
<input type="hidden" name="{{$pondCollection}}_deleted" id="deleted-{{$pondID}}" value="{{empty($pondMedia) ? '1' : ''; }}">
@if($attributes->has('multiple'))
    <input type="hidden" name="{{$pondCollection}}_deleted_ids" id="deleted-{{$pondID}}-ids" value="">
@endif

<script>
    var {{$pondInstanceName}} = FilePond.create(document.getElementById('{{$pondID}}'), {
        allowImagePreview: true
    });

    {{$pondInstanceName}}.setOptions({
        labelIdle: '{!!$pondLable!!} <br> Drag & Drop your file or <span class="filepond--label-action"> Browse </span>',
        server: {
            url: '/uploader',
            process: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    "collection": '{{$pondCollection}}'
                }
            },
            revert: {
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    '_method': 'DELETE',
                    "collection": '{{$pondCollection}}'
                }
            }
        },
        onremovefile(error, file) {
            if (file.origin == 3) {
                $('#deleted-{{$pondID}}').val('1');

                @if($attributes->has('multiple'))
                    const regex = /\/storage\/(\w+)\//;
                    const match = file.source.match(regex);
                    let listOfIds = $('#deleted-{{$pondID}}-ids').val();
                    console.log(match);
                    if(listOfIds==""){
                        $('#deleted-{{$pondID}}-ids').val(match[1]);
                    }else{
                        let ids = listOfIds.split(',');
                        ids.push(match[1]);
                        listOfIds = ids.join(',');
                        $('#deleted-{{$pondID}}-ids').val(listOfIds);
                    }
                @endif
            }
        }
    });

    @if (!empty($pondMedia))
    {{$pondInstanceName}}.setOptions({
        files: @json($pondMedia),
    });
    @endif

</script>
