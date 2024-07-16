<x-dynamic-component
        :component="$getFieldWrapperView()"
        :field="$field"
>

    @php
        $width = $getWidth();
        $height = $getHeight();
        $pointRadius = $getPointRadius() ?? 5;
        $imageUrl = $getImageUrl();
    @endphp
    <div
            x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }"
            x-init="
            const stage = new Konva.Stage({
                container: $refs.containerRef,
                @if($width)
                    width: @js($width),
                @endif

                @if($height)
                    height: @js($height),
                @endif
            });

            const layer = new Konva.Layer();
            stage.add(layer);

            let bodyImage;
            Konva.Image.fromURL(
                @js($imageUrl),
                function (image) {
                image.setAttrs({
                    x: 0,
                    y: 0,
                    @if($width)
                        width: @js($width),
                    @endif

                    @if($height)
                        height: @js($height),
                    @endif
                });
                layer.add(image);
                layer.draw();
                bodyImage = image;
            });

            stage.on('click', function (e) {
                var pointerPosition = stage.getPointerPosition();
                var coordinates = [pointerPosition.x, pointerPosition.y];
                var shape = e.target;

                if (shape !== stage && shape !== bodyImage) {
                    // If the clicked shape is a circle, remove it
                    shape.destroy();
                    layer.draw();

                    // Remove the coordinates from the state
                    let currentState = state || [];
                    currentState = currentState.filter(coord => coord[0] !== shape.attrs.x || coord[1] !== shape.attrs.y);
                    state = currentState;
                } else if (shape === stage || shape === bodyImage) {
                    // Draw a circle at the click location
                    var circle = new Konva.Circle({
                        x: pointerPosition.x,
                        y: pointerPosition.y,
                        radius: @js($pointRadius),
                        fill: 'red',
                        stroke: 'black',
                        strokeWidth: 1,
                    });
                    layer.add(circle);
                    layer.draw();

                    // Update the state with the new coordinates
                    let currentState = state || [];
                    currentState = Array.isArray(currentState) ? currentState : [];
                    currentState.push(coordinates);
                    state = currentState;
                }

                state = stage.toDataURL()
            });

             {{-- document.getElementById('saveButton').addEventListener('click', function () {
                var dataURL = stage.toDataURL();
                console.log(dataURL);
                downloadURI(dataURL, 'stage.png');
            });

            function downloadURI(uri, name) {
                var link = document.createElement('a');
                link.download = name;
                link.href = uri;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } --}}
        "
    >
        <div
                x-ref="containerRef"
                id="container">
        </div>
        <input x-model="state" type="hidden" name="coordinates" id="coordinates">
        {{-- <button type="button" id="saveButton">Save Image</button> --}}
    </div>
</x-dynamic-component>
