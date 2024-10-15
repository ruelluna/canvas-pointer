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
        x-data="{
            coordinates: $wire.$entangle('{{ $getStatePath() }}').defer,
            imageData: '',
            base64Img: '',
        }"
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
                }
            );

            stage.on('click', function (e)
            {
                var pointerPosition = stage.getPointerPosition();
                var shape = e.target;

                if (shape !== stage && shape !== bodyImage) {
                    shape.destroy();
                    layer.draw();

                    // Remove coords from the coordinates array
                    let currentCoordinates = coordinates || [];
                    currentCoordinates = currentCoordinates.filter(
                        coord => coord[0] !== shape.attrs.x || coord[1] !== shape.attrs.y
                    );

                    coordinates = currentCoordinates;
                } else if (shape === stage || shape === bodyImage) {
                    // Draw a circle at the clicked location
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

                    // Add new coords to the coordinates array
                    let currentCoordinates = coordinates || [];
                    currentCoordinates.push([pointerPosition.x, pointerPosition.y]);
                    coordinates = currentCoordinates;
                }

                updateImageData();
                $wire.set('{{ $getStatePath() }}', base64Img);
            });

            function updateImageData()
            {
                base64Img = stage.toDataURL();
            }">
        <div wire:ignore x-ref="containerRef" id="container"></div>
        <input x-model="coordinates" type="hidden" name="coordinates" id="coordinates">
        <input type="hidden" name="canvas_data" :value="base64Img">
    </div>
</x-dynamic-component>
