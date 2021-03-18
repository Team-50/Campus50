<template>
    <FrontLayout>
        <v-container fluid>
            <v-row class="grey lighten-5">
                <v-col cols="12">
                    <!--<v-carousel
                        cycle                                                
                        show-arrows-on-hover>
                        <v-carousel-item
                            v-for="(slide, i) in slides"
                            :key="i"
                            :src="$api.storageURL+'/'+slide.src"
                            style="width:auto;">                            
                        </v-carousel-item>
                    </v-carousel>-->
                    <v-img
                        lazy-src="$api.storageURL/storage/images/sliders/banner1.jpg"
                        max-height=""
                        max-width="100%"
                        :src="$api.url+'/storage/images/sliders/banner1.jpg'"
                    ></v-img>
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
                src:'storage/images/sliders/banner1.jpg',
            }/*,
            {
                src:'storage/images/sliders/slider2.jpg',
            }*/
        ],
    }), 
    components: {
		FrontLayout
	}  
}
</script>