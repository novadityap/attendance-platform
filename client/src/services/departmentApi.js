import axiosBaseQuery from '@/lib/baseQuery';
import { createApi } from '@reduxjs/toolkit/query/react';

const departmentApi = createApi({
  reducerPath: 'departmentApi',
  baseQuery: axiosBaseQuery(),
  tagTypes: ['Department'],
  endpoints: builder => ({
    searchDepartments: builder.query({
      query: params => ({
        url: '/departments/search',
        method: 'GET',
        params
      }),
      providesTags: result =>
        result
          ? [
              ...result.data.map(({ id }) => ({ type: 'Department', id })),
              { type: 'Department', id: 'LIST' }
            ]
          : [{ type: 'Department', id: 'LIST' }]
    }),
    listDepartments: builder.query({
      query: () => ({
        url: '/departments',
        method: 'GET'
      }),
      providesTags: result =>
        result
          ? [
              ...result.data.map(({ id }) => ({ type: 'Department', id })),
              { type: 'Department', id: 'LIST' }
            ]
          : [{ type: 'Department', id: 'LIST' }]
    }),
    showDepartment: builder.query({
      query: departmentId => ({
        url: `/departments/${departmentId}`,
        method: 'GET'
      }),
      providesTags: (result, error, departmentId) => [
        { type: 'Department', id: departmentId }
      ],
    }),
    createDepartment: builder.mutation({
      query: data => ({
        url: '/departments',
        method: 'POST',
        data
      }),
      invalidatesTags: [{ type: 'Department', id: 'LIST' }]
    }),
    updateDepartment: builder.mutation({
      query: ({ departmentId, data }) => ({
        url: `/departments/${departmentId}`,
        method: 'PUT',
        data
      }),
      invalidatesTags: (result, error, { departmentId }) => [
        { type: 'Department', id: departmentId }
      ],
    }),
    removeDepartment: builder.mutation({
      query: departmentId => ({
        url: `/departments/${departmentId}`,
        method: 'DELETE',
      }),
      invalidatesTags: (result, error, departmentId) => [
        { type: 'Department', id: departmentId }
      ],
    }),
  }),
});

export const {
  useSearchDepartmentsQuery,
  useLazySearchDepartmentsQuery,
  useListDepartmentsQuery,
  useLazyListDepartmentsQuery,
  useShowDepartmentQuery,
  useLazyShowDepartmentQuery,
  useCreateDepartmentMutation,
  useUpdateDepartmentMutation,
  useRemoveDepartmentMutation,
} = departmentApi;

export default departmentApi;

