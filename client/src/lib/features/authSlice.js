import { createSlice } from '@reduxjs/toolkit';

const authSlice = createSlice({
  name: 'auth',
  initialState: {
    token: null,
    currentEmployee: null,
  },
  reducers: {
    setToken: (state, action) => {
      state.token = action.payload;
    },
    setCurrentEmployee: (state, action) => {
      state.currentEmployee = {
        'id': action.payload.id,
        'avatar': action.payload.avatar,
        'name': action.payload.name,
        'email': action.payload.email,
        'role': action.payload.role.name,
        'departmentId': action.payload.departmentId,
      };
    },
    updateCurrentEmployee: (state, action) => {
      state.currentEmployee = {
        ...state.currentEmployee,
        ...action.payload,
      };
    },
    clearAuth: (state) => {
      state.token = null;
      state.currentEmployee = null;
    },
  },
});

export const { 
  setToken, 
  setCurrentEmployee, 
  updateCurrentEmployee,
  clearAuth
} = authSlice.actions;
export default authSlice.reducer;
