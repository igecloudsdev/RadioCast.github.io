<template>
    <div ref="$content">
        <slot />
    </div>
</template>

<script setup lang="ts">
import {inject, onUnmounted, ShallowRef, useTemplateRef, watch} from "vue";
import {LatLngTuple, Map, marker, Popup} from "leaflet";

const props = defineProps<{
    position: LatLngTuple
}>();

const $map = inject<ShallowRef<Map | null>>('map');
const map = $map.value;

const mapMarker = marker(props.position);
mapMarker.addTo(map);

const popup = new Popup();
const $content = useTemplateRef('$content');

watch(
    $content,
    (newContent) => {
        popup.setContent(newContent);
        mapMarker.bindPopup(popup);
    },
    {immediate: true}
);

onUnmounted(() => {
    mapMarker.remove();
});
</script>
