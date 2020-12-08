<template>
    <FrontLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-carousel
                        cycle                                                
                        show-arrows-on-hover>
                        <v-carousel-item
                            v-for="(slide, i) in slides"
                            :key="i"
                            :src="$api.url+'/'+slide.src">                            
                        </v-carousel-item>
                    </v-carousel>
                </v-col>
            </v-row>
        </v-container>
    </FrontLayout>
</template>
<script>
import FrontLayout from '@/views/layouts/FrontLayout';
export default {
    name: 'Home',
    data:()=>({
        slides: [
            {
                src:'storage/images/sliders/slider1.jpg',
            },
            {
                src:'storage/images/sliders/slider2.jpg',
            }
        ],
    }), 
    components: {
		FrontLayout
	}  
}
</script>