import axiosBaseQuery from '@/lib/baseQuery';
import { createApi } from '@reduxjs/toolkit/query/react';

const employeeApi = createApi({
  reducerPath: 'employeeApi',
  baseQuery: axiosBaseQuery(),
  tagTypes: ['Employee'],
  endpoints: builder => ({
    searchEmployees: builder.query({
      query: params => ({
        url: '/employees/search',
        method: 'GET',
        params
      }),
      providesTags: result =>
        result
          ? [
              ...result.data.map(({ id }) => ({ type: 'Employee', id })),
              { type: 'Employee', id: 'LIST' }
            ]
          : [{ type: 'Employee', id: 'LIST' }]
    }),
    showEmployee: builder.query({
      query: employeeId => ({
        url: `/employees/${employeeId}`,
        method: 'GET'
      }),
      providesTags: (result, error, employeeId) => [
        { type: 'Employee', id: employeeId }
      ],
    }),
    createEmployee: builder.mutation({
      query: data => ({
        url: '/employees',
        method: 'POST',
        data
      }),
      invalidatesTags: [{ type: 'Employee', id: 'LIST' }]
    }),
    updateEmployee: builder.mutation({
      query: ({ employeeId, data }) => ({
        url: `/employees/${employeeId}`,
        method: 'POST',
        data,
        headers: { 'Content-Type': 'multipart/form-data' }
      }),
      invalidatesTags: (result, error, { employeeId }) => [
        { type: 'Employee', id: employeeId }
      ],
    }),
    removeEmployee: builder.mutation({
      query: employeeId => ({
        url: `/employees/${employeeId}`,
        method: 'DELETE',
      }),
      invalidatesTags: (result, error, employeeId) => [
        { type: 'Employee', id: employeeId }
      ],
    }),
  }),
});

export const {
  useSearchEmployeesQuery,
  useLazySearchEmployeesQuery,
  useShowEmployeeQuery,
  useLazyShowEmployeeQuery,
  useCreateEmployeeMutation,
  useUpdateEmployeeMutation,
  useRemoveEmployeeMutation,
} = employeeApi;

export default employeeApi;
