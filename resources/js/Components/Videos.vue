<script setup>
// <!-- https://github.surmon.me/videojs-player -->

import Button from 'primevue/button';
import { computed, onUpdated, ref, watch, watchEffect } from 'vue';
import '@videojs/themes/dist/forest/index.css';
import 'video.js/dist/video-js.css'

const props = defineProps({
    src:String,
    poster:String,
    activeBrood: Number,
    stop: Boolean,
    muted: Boolean,
    height: Number,
    width: Number,
    tab:String,
    updateVideo:Boolean|Function,
})
const model = defineModel('updateVideo')
const emits = defineEmits([
    'update:updateVideo',
])
const middle = computed(() => {
    return props.height/2 + 100 
})
const handleMounted = (event) => {
    player.value = event.player
    state.value = event.state
    player.value.bufferedPercent(0,1)
}
const config = {
    loop:true,
    autopĺay:false,
    volume:0.8,
    muted:false,
}
const state = ref()
const player = ref()
const stopIcon = ['pi pi-pause','pi pi-play']
const stopIconActive = ref('pi pi-play')
const isStoped = ref(true)
const stopPlayer = () => {
    if ( player.value?.paused()){
        player.value?.play()
        playSpin.value = false
        isStoped.value = false
        stopIconActive.value = stopIcon[0]
        emits('update:updateVideo',true)
    }else{
            player.value?.pause()
            stopIconActive.value = stopIcon[1]
            isStoped.value = true
            emits('update:updateVideo',false)
    }
}
const mutedVideo = ref(false)
const mutedIcon = ['pi pi-volume-off','pi pi-volume-up']
const mutedIconActive = ref('pi pi-volume-up')
const mutedPlayer = () => {
    mutedVideo.value = !mutedVideo.value
    mutedIconActive.value = mutedIcon[mutedVideo.value ? 0 : 1]
}
const isActiveControls = ref(false) 
const handlePlay = (e) => {
    isActiveControls.value = true
}
const playSpin = ref(true)
watchEffect(() => {
    if ( ! props.stop ){
        player.value?.pause()
        stopIconActive.value = stopIcon[1]
    }
})
</script>
<template>
    <video-player
        :class="['video-player', 'vjs-theme-forest', 'vjs-big-play-centered', { loading: !state }, 'transition-transform']"
        :src="props.src"
        :autoplay="config.autoplay"
        v-model:muted="mutedVideo"
        :loop="config.loop"
        :volume="config.volume" 
        :enableSmoothSeeking="true"
        :width="props.width"
        :height="props.height"
        @mounted="handleMounted"
        @playing="handlePlay"
        :techOrder="['html5']"
        :controls="false"
        :control-bar=[false]
        />
        <!-- playsinline -->
         <div class="absolute start-1/3 top-1/2">
            <Button         
                type="button" 
                severity="contrast" 
                @click="stopPlayer()" 
                aria-label="Play/Stop" 
                variant="text"
                rounded
                :style="isStoped ? 'display:block' : 'display:none'"
                class="transition-all duration-200 ease-linear"
                >
                <i class="pi pi-play p-4 " style="font-size: 4rem;"></i>
            </Button>
         </div>
        <div class="absolute right-4" :style="{'bottom':middle + 'px'}"
        >
            <div class="grid grid-flow-row gap-4">
                <Button 
                    :icon="stopIconActive"  
                    
                    class="hide"
                    type="button" 
                    severity="contrast" 
                    @click="stopPlayer()" 
                    aria-label="Play/Stop" 
                    size=""
                    rounded />
                <Button 
                    :icon="mutedIconActive"  
                    class="" 
                    type="button" 
                    severity="contrast" 
                    @click="mutedPlayer()" 
                    aria-label="Muted" 
                    size=""
                    rounded />

            </div>
        </div>
</template>
<style lang="css" scoped>

</style>