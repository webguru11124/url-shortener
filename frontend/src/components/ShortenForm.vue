
<template>
    <div class="w-full max-w-md mx-auto">
        <h2 class="text-2xl font-bold mb-4">URL Shortener</h2>
        <form @submit.prevent="submitUrl" class="space-y-4">
            <div class="form-group">
                <label for="url" class="block">Enter URL:</label>
                <input 
                    type="text" 
                    id="url" 
                    v-model="url" 
                    placeholder="https://php.test.com" 
                    required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-400 text-black" 
                />
            </div>
            <div class="form-group">
                <label for="subdir" class="block">Enter SubDirectory (Optional):</label>
                <input 
                    type="text" 
                    id="subdir" 
                    v-model="subdir"
                    placeholder="something" 
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-400 text-black" 
                />
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Shorten
            </button>
        </form>
    </div>
</template>

<script setup lang="ts">
    import { ref } from 'vue';

    import { saveUrl } from '../composables/ShortenUrl.vue';

    const redirectUrl = defineModel('redirectUrl');
    
    const url = ref('');
    const subdir = ref('');

    const submitUrl = async () => {
        redirectUrl.value = await saveUrl(url.value, subdir.value);
    }
</script>