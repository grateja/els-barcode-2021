import FormHelper from '../../helpers/FormHelper.js';
const state = {
    errors: FormHelper,
    loggingIn: false,
    loggingOut: false,
    isLoading: false
}

const getters = {
    getErrors(state) {
        return state.errors;
    },
    getLoggingIn(state) {
        return state.loggingIn;
    },
    getLoggingOut(state) {
        return state.loggingOut;
    },
    isLoading(state) {
        return state.isLoading;
    }
}

const mutations = {
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    setLoggingIn(state, status) {
        state.loggingIn = status;
    },
    setLoggingOut(state, status) {
        state.loggingOut = status;
    },
    setLoadingStatus(state, status) {
        state.isLoading = status;
    }
}

const actions = {
    loginAttempt(context, request) {
        context.commit('setLoggingIn', true);
        context.commit('clearErrors');
        return axios.post('/api/oauth/login', request).then((res, rej) => {
            context.dispatch('setAuth', res.data, {root: true});
            context.commit('setLoggingIn', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err);
            if(err.response.status == 401) {
                context.commit('setErrors', err.response.data.errors);
            }
            context.commit('setLoggingIn', false);
            return Promise.reject(err);
        });
    },
    logout(context) {
        context.commit('setLoggingOut', true);
        return axios.post('/api/oauth/logout').then((r) => {
            context.commit('clearUser', null, {root: true});
            context.commit('clearToken', null, {root: true});
            context.commit('setLoggingOut', false);
        }).catch(err => {
            context.commit('setLoggingOut', false);
            return Promise.reject(err);
        });
    },
    checkToken(context, request) {
        if(!localStorage.getItem('token')) {
            return Promise.reject('Authentication failed');
        }
        context.commit('setLoadingStatus', true);
        return axios.get('/api/oauth/check').then((res, rej) => {
            context.commit('setUser', res.data, {root: true});
            context.commit('setLoadingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setLoadingStatus', false);
            return Promise.reject(err);
        });
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
