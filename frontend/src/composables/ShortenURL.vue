<script lang="ts">
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const server_url = import.meta.env.VITE_API_BACKEND_URL;
const shortened_prefix = import.meta.env.VITE_API_SHORTENED_PREFIX;

export const saveUrl: String = async (url: string, subdir: string) => {
    let shortened = null;
    await axios.post( server_url + '/api/shorten-url', {url: url, subdir: subdir})
        .then((response: any) => {
        if(response.data.status === 'exist')
        {
            showToast("This URL is registered already."); 
            if(response.data.sub){
                shortened = shortened_prefix + response.data.sub + '/' + response.data.hash;
            } else {
                shortened = shortened_prefix + response.data.hash;
            }
        } else if(response.data.status === "error") {
            showToast("This URL is not safe over the network"); 
        } else {
            showToast("Success!");
            if(response.data.sub){
                shortened = shortened_prefix + response.data.sub + '/' + response.data.hash;
            } else {
                shortened = shortened_prefix + response.data.hash;
            }
        }
        })
        .catch((error: any) => {
            showToast("Occured error in connection to Server"); 
        });
    return shortened;
}

function showToast( msg: string ) {
  toast( msg, {
        autoClose: 1000,
      }); 
};

</script>