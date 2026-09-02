@php
    $manifestPath = public_path('build/manifest.json');
    $fontUrls = [];
    if (is_file($manifestPath)) {
        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        if (is_array($manifest)) {
            $seen = [];
            foreach ($manifest as $entry) {
                $file = $entry['file'] ?? '';
                if (str_ends_with((string) $file, '.woff2')) {
                    $key = basename((string) $file);
                    if (! isset($seen[$key])) {
                        $seen[$key] = true;
                        $fontUrls[] = asset('build/'.$file);
                    }
                }
            }
        }
    }
@endphp
@foreach ($fontUrls as $fontUrl)
    <link rel="preload" href="{{ $fontUrl }}" as="font" type="font/woff2" crossorigin>
@endforeach