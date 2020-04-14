import store from './store/index.js';
import router from './router.js';

store.dispatch('auth/checkToken').then((res, rej) => {
    // console.log(res.data);
}).catch(err => {
    router.push('/login');
    console.log(err);
});

axios.interceptors.response.use(null, err => {
    console.log('keme lang');
    if(err && err.response && err.response.status == 401) {
        store.commit('setFlash', {
            message: 'Anauthorized',
            color: 'error'
        });
    // alert('Unauthorized');
        // store.commit('setFlash', {message: 'Unauthorized', color: 'error'});
    } else if(err && err.response && err.response.status == 422) {
        let data = err.response.data;
        if(data instanceof Blob) {
            let fr = new FileReader();
            fr.addEventListener('loadend', (e) => {
                let result = JSON.parse(e.srcElement.result);
                setFlash(result.errors);
            });
            fr.readAsText(err.response.data);
        } else {
            setFlash(err.response.data.errors);
        }
    }
    return Promise.reject(err);
});

function setFlash(errors) {
    if(errors)
        if(errors.hasOwnProperty('message')) {
            store.commit('setFlash', {
                message: errors.message[0],
                color: 'error'
            });
        }
}
