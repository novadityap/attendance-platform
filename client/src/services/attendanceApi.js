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
        params,
      }),
      providesTags: result =>
        result
          ? [
              ...result.data.map(({ id }) => ({ type: 'Attendance', id })),
              { type: 'Attendance', id: 'LIST' },
            ]
          : [{ type: 'Attendance', id: 'LIST' }],
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
    todayAttendance: builder.query({
      query: () => ({
        url: '/attendances/today',
        method: 'GET',
      }),
      providesTags: [{ type: 'Attendance', id: 'TODAY' }],
    }),
    checkInAttendance: builder.mutation({
      query: () => ({
        url: '/attendances/checkIn',
        method: 'POST',
      }),
      invalidatesTags: [
        { type: 'Attendance', id: 'TODAY' },
        { type: 'Attendance', id: 'LIST' },
      ],
    }),
    checkOutAttendance: builder.mutation({
      query: () => ({
        url: '/attendances/checkOut',
        method: 'PATCH',
      }),
      invalidatesTags: [
        { type: 'Attendance', id: 'TODAY' },
        { type: 'Attendance', id: 'LIST' },
      ],
    }),
  }),
});

export const {
  useSearchAttendancesQuery,
  useLazySearchAttendancesQuery,
  useRemoveAttendanceMutation,
  useTodayAttendanceQuery,
  useCheckInAttendanceMutation,
  useCheckOutAttendanceMutation,
} = attendanceApi;

export default attendanceApi;
