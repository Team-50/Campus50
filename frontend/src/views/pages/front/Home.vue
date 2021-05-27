<template>
		<FrontLayout>
				<v-container fluid>
						<v-row class="white lighten-2">
								<v-col cols="12" class="hidden-sm-and-down pa-0">
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
												class="hidden-sm-and-down"
												lazy-src="$api.storageURL/storage/images/sliders/banner3.jpg"
												max-height="auto"
												max-width="100%"
												:src="$api.url+'/storage/images/sliders/banner3.jpg'"
										></v-img>
										<br>
										<v-img
												class="hidden-sm-and-down"
												lazy-src="$api.storageURL/storage/images/sliders/banner4.jpg"
												max-height="auto"
												max-width="100%"
												:src="$api.url+'/storage/images/sliders/banner4.jpg'"
										></v-img>
										<br>
										<v-img
												class="hidden-sm-and-down"
												lazy-src="$api.storageURL/storage/images/sliders/banner5.jpg"
												max-height="auto"
												max-width="100%"
												:src="$api.url+'/storage/images/sliders/banner5.jpg'"
										></v-img>
								</v-col>

								<v-col cols="12" class="hidden-lg-and-up pa-0">
										<v-img
												class="hidden-lg-and-up mb-2"
												lazy-src="$api.storageURL/storage/images/sliders/banner3_1.1.jpg"
												max-height="auto"
												max-width="100%"
												:src="$api.url+'/storage/images/sliders/banner3_1.1.jpg'"
										></v-img>
										<v-img
												class="hidden-lg-and-up mb-2"
												lazy-src="$api.storageURL/storage/images/sliders/banner4_1.1.jpg"
												max-height="auto"
												max-width="100%"
												:src="$api.url+'/storage/images/sliders/banner4_1.1.jpg'"
										></v-img>
										<v-img
												class="hidden-lg-and-up"
												lazy-src="$api.storageURL/storage/images/sliders/banner5.jpg"
												max-height="auto"
												max-width="100%"
												:src="$api.url+'/storage/images/sliders/banner5.jpg'"
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