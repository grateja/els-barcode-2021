<template>
    <v-dialog :value="value" max-width="480" persistent>
        <v-card>
            <v-card-title class="title">Select picture</v-card-title>
            <v-card-text>
                <v-responsive v-if="prevUrl" :aspect-ratio="16/9" max-height="300">
                    <v-img :src="prevUrl"></v-img>
                </v-responsive>
            </v-card-text>
            <v-card-actions>
                <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setPicture" accept="image/*">
                <v-btn @click="browsePicture" class="primary"><v-icon left>photo</v-icon> {{prevUrl ? 'change picture' : 'select picture'}}</v-btn>
                <v-spacer></v-spacer>
                <v-btn class="primary" @click="ok">OK</v-btn>
                <v-btn @click="close">Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'url'
    ],
    data() {
        return {
            file: null
        }
    },
    methods: {
        browsePicture() {
            this.$refs.inputFile.click();
        },
        setPicture(e) {
            if(e.target.files.length == 1) {
                this.file = e.target.files[0];
            }
        },
        close() {
            this.$emit('input', false);
            this.file = null;
        },
        ok() {
            let formData = new FormData;
            formData.append('file', this.file);
            this.$emit('ok', formData);
            this.close();
        }
    },
    computed: {
        prevUrl() {
            if(!!this.file) {
                return URL.createObjectURL(this.file);
            } else {
                return this.url;
            }
        }
    }
}
</script>
<style scoped>
#inputFile {
    display: none;
}
</style>
