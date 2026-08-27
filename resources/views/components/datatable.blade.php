{{-- resources/views/components/datatable.blade.php --}}
@props([
    'id' => 'table_' . uniqid(),
    'headers' => [],
    'noPaging' => false,
    'export' => false,
    'pageLength' => 10,
    'striped' => true,
    'class' => ''
])

<div class="table-responsive">
    <table 
        id="{{ $id }}" 
        {{ $attributes->merge([
            'class' => 'table align-middle table-hover mb-0 datatable ' . ($striped ? 'table-striped ' : '') . $class
        ]) }}
        @if($noPaging) data-no-paging="true" @endif
        @if($export) data-export="true" @endif
        data-page-length="{{ $pageLength }}"
    >
        @if(count($headers) > 0)
            <thead class="table-light">
                <tr>
                    @foreach($headers as $header)
                        @if(is_array($header))
                            <th class="{{ $header['class'] ?? '' }}">{{ $header['label'] ?? '' }}</th>
                        @else
                            <th>{{ $header }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
        @else
            {{ $thead ?? '' }}
        @endif
        
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
