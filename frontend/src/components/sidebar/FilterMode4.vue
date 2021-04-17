<template>
	<v-list-item>
		<v-list-item-content>
			<v-select
				v-model="prodi_id"
				:items="daftar_prodi"                
				item-text="text"
				item-value="id"
				label="PROGRAM STUDI"
				outlined />
		</v-list-item-content>
	</v-list-item>	
</template>
<script>
	export default {
		name: "FilterMode4",
		created() {
			this.daftar_prodi=this.$store.getters['uiadmin/getDaftarProdi'];
			this.prodi_id=this.$store.getters["uiadmin/getProdiID"];
		},
		data: () => ({
			firstloading: true,
			daftar_prodi: [],
			prodi_id: null,
		}),
		methods: {
			setFirstTimeLoading(bool) {
				this.firstloading=bool;
			}
		},
		watch: {		
			prodi_id(val) {
				if (!this.firstloading) {
					this.$store.dispatch("uiadmin/updateProdi",val);
					this.$emit("changeProdi",val);        
				}
			},			
		}
	}
</script>
