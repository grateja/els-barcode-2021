import Vuex from 'vuex';
import Vue from 'vue';
Vue.use(Vuex);

import auth from './modules/auth.js';
import account from './modules/account.js';
import user from './modules/user.js';
import exportdownload from './modules/exportdownload.js';
import printer from './modules/printer.js';

import incomingFinishedGoodReport from './modules/incomingFinishedGoodReport.js';
import incomingSparePartReport from './modules/incomingSparePartReport.js';
import finishedGood from './modules/finishedGood.js';
import sparePart from './modules/sparePart.js';

import reservation from './modules/reservation.js';
import client from './modules/client.js';
import subdealer from './modules/subdealer.js';

import outgoingFinishedGoodReport from './modules/outgoingFinishedGoodReport.js';
import outgoingSparePartReport from './modules/outgoingSparePartReport.js';

import finishedGoodProfile from './modules/finishedGoodProfile.js';
import sparePartProfile from './modules/sparePartProfile.js';
import fixedAssetAccount from './modules/fixedAssetAccount.js';
import fixedAsset from './modules/fixedAsset.js';

export default new  Vuex.Store({
    state: {
        currentUser: null,
        flashMessage: null
    },
    getters: {
        getCurrentUser(state) {
            return state.currentUser;
        },
        getFlashMessage(state) {
            return state.flashMessage;
        }
    },
    actions: {
        setAuth(context, data) {
            context.commit('setUser', data);
            if(data.retainToken) {
                localStorage.setItem('token', data.token.accessToken);
            } else {
                localStorage.removeItem('token');
            }
        }
    },
    mutations: {
        setUser(state, data) {
            // console.log('set user asfsdf sf a', data.user)
            state.currentUser = data.user;
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${data.token.accessToken}`;
        },
        updateEmail(state, data) {
            state.currentUser.email = data.email;
        },
        clearUser(state) {
            state.currentUser = null;
        },
        clearToken(state) {
            window.axios.defaults.headers.common['Authorization'] = null;
            localStorage.removeItem('token');
        },
        updateName(state, user) {
            state.currentUser.fullname = user.fullname;
        },
        setFlash(state, config) {
            state.flashMessage = config;
        }
    },
    modules: {
        auth,
        account,
        user,
        exportdownload,
        printer,
        incomingFinishedGoodReport,
        finishedGood,
        incomingSparePartReport,
        sparePart,
        reservation,
        client,
        subdealer,
        outgoingFinishedGoodReport,
        outgoingSparePartReport,
        finishedGoodProfile,
        sparePartProfile,
        fixedAssetAccount,
        fixedAsset
    }
});
