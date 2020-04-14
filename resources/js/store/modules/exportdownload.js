import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isLoading: false
};

const mutations = {
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    download(context, data) {
        context.commit('setLoadingStatus', true);
        context.commit('clearErrors');
        return axios.get(`/api/reports/excel/${data.uri}`, {
            params: data.params,
            responseType: 'blob'
        }).then((res, rej) => {
            console.log(res.data);
            const downloadUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.setAttribute('download', 'file.xls'); //any other extension
            document.body.appendChild(link);
            link.click();
            link.remove();
            context.commit('setLoadingStatus', false);
            return res;
        }).catch(err => {
            let fr = new FileReader();
            fr.addEventListener('loadend', (e) => {
                let result = JSON.parse(e.srcElement.result)
                console.log(result.errors);
                context.commit('setErrors', result.errors);
                context.commit('setLoadingStatus', false);
            });
            fr.readAsText(err.response.data);
            return Promise.reject(err);
        });
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    },
    isLoading(state) {
        return state.isLoading;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
