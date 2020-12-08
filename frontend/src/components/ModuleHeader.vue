<template>
    <v-container :fluid="isReportPage">        
        <v-row no-gutters>
            <v-col cols="12" xs="12" sm="12" md="12">
                <slot name="breadcrumbs"/>
            </v-col>
        </v-row>
        <v-row no-gutters>            
            <v-col cols="12" xs="12" sm="12" md="12">
                <h1 class="subheading grey--text">
                    <v-icon large>
                        <slot name="icon" />
                    </v-icon>
                    <slot name="name" />
                </h1>                
            </v-col>
            <v-col cols="12" xs="12" sm="12" md="12">
                <span class="ml-10 subtitle-1 green--text font-weight-bold">                    
                    <slot name="subtitle" />
                </span>                
            </v-col>
        </v-row>
        <v-row no-gutters>
            <v-col cols="12" xs="12" sm="12" md="12">
                <slot name="desc" />
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
export default {
    name:'ModuleHeader',
    computed : {        
        isReportPage ()
		{
			return true;
		},
    }
}
</script>