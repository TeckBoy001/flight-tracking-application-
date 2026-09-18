{{-- Reusable admin map picker for a flight's current location.
     Two ways to set it, as required: type coordinates directly, or
     click/drag the marker on the map — both write to the same
     current_latitude / current_longitude inputs that get submitted
     with the rest of the flight form. --}}
@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #location-map { height: 340px; border-radius: 0.75rem; }
        .leaflet-container { font-family: inherit; }
    </style>
@endpush

<div>
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium">Current aircraft location</label>
        <span class="text-xs text-gray-400">Click the map, or drag the marker</span>
    </div>

    <div id="location-map" class="border"></div>

    <div class="grid grid-cols-2 gap-4 mt-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Latitude</label>
            <input type="number" step="any" id="current_latitude" name="current_latitude"
                   value="{{ old('current_latitude', $flight->current_latitude) }}"
                   class="w-full border rounded-md px-3 py-2">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Longitude</label>
            <input type="number" step="any" id="current_longitude" name="current_longitude"
                   value="{{ old('current_longitude', $flight->current_longitude) }}"
                   class="w-full border rounded-md px-3 py-2">
        </div>
        <div class="col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Location label (optional, shown to customers instead of raw coordinates)</label>
            <input type="text" name="current_location_label"
                   value="{{ old('current_location_label', $flight->current_location_label) }}"
                   placeholder="e.g. Over the Atlantic Ocean"
                   class="w-full border rounded-md px-3 py-2">
        </div>
        <div class="col-span-2">
            <button type="button" id="clear-location" class="text-xs text-gray-400 hover:text-red-600">
                Clear current location
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const depLat = {{ $flight->departure_latitude ?? 'null' }};
            const depLng = {{ $flight->departure_longitude ?? 'null' }};
            const arrLat = {{ $flight->arrival_latitude ?? 'null' }};
            const arrLng = {{ $flight->arrival_longitude ?? 'null' }};
            const curLatInput = document.getElementById('current_latitude');
            const curLngInput = document.getElementById('current_longitude');

            const startLat = parseFloat(curLatInput.value) || depLat || 20;
            const startLng = parseFloat(curLngInput.value) || depLng || 0;

            const map = L.map('location-map').setView([startLat, startLng], (curLatInput.value ? 5 : 2));

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(map);

            const airportIcon = L.divIcon({
                className: '',
                html: '<div style="width:10px;height:10px;border-radius:9999px;background:#1e293b;border:2px solid white;box-shadow:0 0 0 1px #1e293b;"></div>',
                iconSize: [10, 10],
            });

            const planeIcon = L.divIcon({
                className: '',
                html: '<div style="font-size:22px;line-height:1;transform:rotate(45deg);">&#9992;&#65039;</div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
            });

            if (depLat && depLng) {
                L.marker([depLat, depLng], { icon: airportIcon, interactive: false }).addTo(map).bindTooltip('Departure', { permanent: false });
            }
            if (arrLat && arrLng) {
                L.marker([arrLat, arrLng], { icon: airportIcon, interactive: false }).addTo(map).bindTooltip('Destination', { permanent: false });
            }
            if (depLat && depLng && arrLat && arrLng) {
                L.polyline([[depLat, depLng], [arrLat, arrLng]], { color: '#94a3b8', weight: 2, dashArray: '6 6' }).addTo(map);
            }

            let marker = null;

            function placeMarker(lat, lng) {
                lat = parseFloat(lat);
                lng = parseFloat(lng);
                if (Number.isNaN(lat) || Number.isNaN(lng)) return;

                curLatInput.value = lat.toFixed(6);
                curLngInput.value = lng.toFixed(6);

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { icon: planeIcon, draggable: true }).addTo(map);
                    marker.on('dragend', function (e) {
                        const pos = e.target.getLatLng();
                        placeMarker(pos.lat, pos.lng);
                    });
                }
            }

            if (curLatInput.value && curLngInput.value) {
                placeMarker(curLatInput.value, curLngInput.value);
            }

            map.on('click', function (e) {
                placeMarker(e.latlng.lat, e.latlng.lng);
            });

            [curLatInput, curLngInput].forEach(function (input) {
                input.addEventListener('change', function () {
                    if (curLatInput.value && curLngInput.value) {
                        placeMarker(curLatInput.value, curLngInput.value);
                        map.setView([parseFloat(curLatInput.value), parseFloat(curLngInput.value)], Math.max(map.getZoom(), 5));
                    }
                });
            });

            document.getElementById('clear-location').addEventListener('click', function () {
                curLatInput.value = '';
                curLngInput.value = '';
                if (marker) {
                    map.removeLayer(marker);
                    marker = null;
                }
            });

            // Leaflet sometimes measures its container before the layout
            // (e.g. inside a grid) has settled — nudge it once after paint.
            setTimeout(function () { map.invalidateSize(); }, 200);
        })();
    </script>
@endpush
