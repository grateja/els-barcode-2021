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
    setLoadingClaimStub(state, status) {
        state.claimStubLoading = status;
    },
    setLoadingJobOrder(state, status) {
        state.jobOrderLoading = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    printClaimStub(context, data) {
        context.commit('setLoadingClaimStub', true);
        context.commit('clearErrors');
        return axios.get(`/api/transactions/${data.transactionId}/print-claim-stub`).then((res, rej) => {
            let w = window.open('about:blank', 'print', 'width=800,height=1000');

            w.document.write(res.data);
            w.document.close();

            context.commit('setLoadingClaimStub', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingClaimStub', false);
            return Promise.reject(err);
        });
    },
    printJobOrder(context, transactionId) {
        context.commit('setLoadingJobOrder', true);
        context.commit('clearErrors');
        return axios.get(`/api/transactions/${transactionId}/print-job-order`).then((res, rej) => {
            let w = window.open('about:blank', 'print', 'width=800,height=1000');

            w.document.write(res.data);
            w.document.close();

            context.commit('setLoadingJobOrder', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingJobOrder', false);
            return Promise.reject(err);
        });
    },
    rfidLoadTransaction(context, transactionId) {
        context.commit('clearErrors');
        return axios.get(`/api/rfid-cards/load-transactions/${transactionId}/print-load-transaction`).then((res, rej) => {
            let w = window.open('about:blank', 'print', 'width=800,height=1000');

            w.document.write(res.data);
            w.document.close();
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    posCollections(context, query) {
        context.commit('clearErrors');
        return axios.get('/api/reports/print/pos-collections', {
            params: query
        }).then((res, rej) => {
            let params = 'fullscreen=yes,height=' + screen.height + ',width=' + screen.width;
            console.log(params)
            let w = window.open('about:blank', 'print', params);

            w.document.write(res.data);
            w.document.close();

            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingJobOrder', false);
            return Promise.reject(err);
        });
    },
    posTransactions(context, query) {
        context.commit('clearErrors');
        return axios.get('/api/reports/print/pos-transactions', {
            params: query
        }).then((res, rej) => {
            let params = 'fullscreen=yes,height=' + screen.height + ',width=' + screen.width;
            console.log(params)
            let w = window.open('about:blank', 'print', params);

            w.document.write(res.data);
            w.document.close();

            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingJobOrder', false);
            return Promise.reject(err);
        });
    },
    rfidTransactions(context, query) {
        context.commit('clearErrors');
        return axios.get('/api/reports/print/rfid-transactions', {
            params: query
        }).then((res, rej) => {
            let params = 'fullscreen=yes,height=' + screen.height + ',width=' + screen.width;
            console.log(params)
            let w = window.open('about:blank', 'print', params);

            w.document.write(res.data);
            w.document.close();

            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingJobOrder', false);
            return Promise.reject(err);
        });
    },
    rfidLoadTransactions(context, query) {
        context.commit('clearErrors');
        return axios.get('/api/reports/print/rfid-load-transactions', {
            params: query
        }).then((res, rej) => {
            let params = 'fullscreen=yes,height=' + screen.height + ',width=' + screen.width;
            console.log(params)
            let w = window.open('about:blank', 'print', params);

            w.document.write(res.data);
            w.document.close();

            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingJobOrder', false);
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
    },
    claimStubLoading(state) {
        return state.claimStubLoading;
    },
    jobOrderLoading(state) {
        return state.jobOrderLoading;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
