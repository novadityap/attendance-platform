import axiosBaseQuery from '@/lib/baseQuery';
import { createApi } from '@reduxjs/toolkit/query/react';

const attendanceApi = createApi({
  reducerPath: 'attendanceApi',
  baseQuery: axiosBaseQuery(),
  tagTypes: ['Attendance'],
  endpoints: builder => ({
    searchAttendances: builder.query({
      query: params => ({
        url: '/attendances/search',
        method: 'GET',
        params
      }),
      providesTags: result =>
        result
          ? [
              ...result.data.map(({ id }) => ({ type: 'Attendance', id })),
              { type: 'Attendance', id: 'LIST' }
            ]
          : [{ type: 'Attendance', id: 'LIST' }]
    }),
    removeAttendance: builder.mutation({
      query: attendanceId => ({
        url: `/attendances/${attendanceId}`,
        method: 'DELETE',
      }),
      invalidatesTags: (result, error, attendanceId) => [
        { type: 'Attendance', id: attendanceId },
      ],
    }),
    checkInAttendance: builder.mutation({
      query: data => ({
        url: '/attendances/checkin',
        method: 'POST',
      }),
      invalidatesTags: [{ type: 'Attendance', id: 'LIST' }]
    }),
    checkOutAttendance: builder.mutation({
      query: data => ({
        url: '/attendances/checkout',
        method: 'PATCH',
      }),
      invalidatesTags: (result, error, { attendanceId }) => [
        { type: 'Attendance', id: attendanceId }
      ],
    }),
  }),
});

export const {
  useSearchAttendancesQuery,
  useLazySearchAttendancesQuery,
  useRemoveAttendanceMutation,
  useCheckInAttendanceMutation,
  useCheckOutAttendanceMutation
} = attendanceApi;

export default attendanceApi;

