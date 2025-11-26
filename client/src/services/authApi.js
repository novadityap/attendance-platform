import axiosBaseQuery from '@/lib/baseQuery.js';
import { createApi } from '@reduxjs/toolkit/query/react';

const authApi = createApi({
  reducerPath: 'authApi',
  baseQuery: axiosBaseQuery(),
  endpoints: builder => ({
    signin: builder.mutation({
      query: data => ({
        url: '/auth/signin',
        method: 'POST',
        data,
      }),
    }),
    signout: builder.mutation({
      query: () => ({
        url: '/auth/signout',
        method: 'POST',
      }),
    }),
    refreshToken: builder.mutation({
      query: () => ({
        url: '/auth/refresh-token',
        method: 'POST',
      }),
    }),
    requestResetPassword: builder.mutation({
      query: data => ({
        url: '/auth/request-reset-password',
        method: 'POST',
        data,
      }),
    }),
    resetPassword: builder.mutation({
      query: ({ data, resetToken }) => ({
        url: `/auth/reset-password/${resetToken}`,
        method: 'POST',
        data,
      }),
    }),
  }),
});

export const {
  useSigninMutation,
  useSignoutMutation,
  useRefreshTokenMutation,
  useRequestResetPasswordMutation,
  useResetPasswordMutation
} = authApi;

export default authApi;
