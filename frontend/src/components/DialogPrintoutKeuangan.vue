<template>
    <v-dialog v-model="dialog" max-width="500px" @keydown.esc="cancel">
        <v-card>
            <v-toolbar dark color="primary" dense flat>
                <v-toolbar-title class="white--text">
                    Printout {{title}}        
                </v-toolbar-title>
                <v-spacer></v-spacer> 
                <v-icon>mdi-printer</v-icon> 
            </v-toolbar>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    name: "DialogPrintoutKeuangan",
    props: {
        pid: {
            type: String,
            required: true
        },
        title: {
            type: String,
            default: ""
        },
    },
    data: () => ({
        dialog: false
    }),
    methods: {
        open()
        {
            this.dialog=true;
        },
        cancel ()
        {
            this.resolve(false)
            this.dialog = false
        }
    }
}
</script>