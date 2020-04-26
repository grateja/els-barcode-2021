import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isLoading: false,
    claimStubLoading: false,
    jobOrderLoading: false
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
    printSerialNumbers(context, data) {
        context.commit('setLoadingStatus', true);
        context.commit('clearErrors');
        return axios.get(`/api/printer/serial-numbers`, {
            params: data.params
        }).then((res, rej) => {
            let w = window.open('about:blank', 'print', 'width=800,height=1000');

            w.document.write(res.data);
            w.document.close();

            context.commit('setLoadingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingStatus', false);
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
