import {configureStore} from "@reduxjs/toolkit";
import { 
  persistStore, 
  persistReducer,
  FLUSH,
  REHYDRATE,
  PAUSE,
  PERSIST,
  PURGE,
  REGISTER,
} from 'redux-persist';
import { combineReducers } from 'redux';
import storage from 'redux-persist/lib/storage';
import authReducer from '@/lib/features/authSlice.js';
import authApi from '@/services/authApi.js';
import employeeApi from '@/services/employeeApi.js';
import departmentApi from '@/services/departmentApi.js';
import attendanceApi from '@/services/attendanceApi.js';
import roleApi from '@/services/roleApi.js';
import dashboardApi from '@/services/dashboardApi.js';

const rootPersistConfig = {
  key: 'root',
  storage,
  whitelist: ['auth']
}

const rootReducer = combineReducers({
  [authApi.reducerPath]: authApi.reducer,
  [employeeApi.reducerPath]: employeeApi.reducer,
  [departmentApi.reducerPath]: departmentApi.reducer,
  [attendanceApi.reducerPath]: attendanceApi.reducer,
  [roleApi.reducerPath]: roleApi.reducer,
  [dashboardApi.reducerPath]: dashboardApi.reducer,
  auth: authReducer,
});

export const store = configureStore({
  reducer: persistReducer(rootPersistConfig, rootReducer),
  middleware: (getDefaultMiddleware) => getDefaultMiddleware({
    serializableCheck: {
      ignoredActions: [FLUSH, REHYDRATE, PAUSE, PERSIST, PURGE, REGISTER],
    }
  }).concat(
    authApi.middleware, 
    employeeApi.middleware,
    departmentApi.middleware,
    attendanceApi.middleware,
    roleApi.middleware,
    dashboardApi.middleware
  ),
});

export const persistor = persistStore(store);
