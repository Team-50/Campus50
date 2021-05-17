<template>
    <div>
        <v-btn color="primary" icon outlined small class="ma-2" @click.stop="tambahItem">
            <v-icon>mdi-plus</v-icon>
        </v-btn>             
        <v-tooltip bottom>             
            <template v-slot:activator="{ on, attrs }">                  
                <v-btn 
                    v-bind="attrs"
                    v-on="on"
                    color="primary" 
                    icon 
                    outlined 
                    small 
                    class="ma-2" 
                    @click.stop="actions"
                        :disabled="!datatable > 0">
                    <v-icon>mdi-plus</v-icon>
                </v-btn>     
            </template>
            <span>keterangan</span>        
        </v-tooltip>
    </div>
</template>