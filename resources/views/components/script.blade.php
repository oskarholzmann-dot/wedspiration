@props(['src'])

{{-- A script from public/ with its change time in the URL, so browsers load the new version after every change --}}
<script src="{{ asset($src) }}?v={{ filemtime(public_path($src)) }}" defer></script>
