'use client';

import DataTable from '@/components/ui/DataTable';
import { createColumnHelper } from '@tanstack/react-table';
import {
  useSearchAttendancesQuery,
  useRemoveAttendanceMutation,
} from '@/services/attendanceApi.js';
import BreadcrumbNav from '@/components/ui/BreadcrumbNav';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/shadcn/card';
import AuthGuard from '@/components/auth/AuthGuard';
import dayjs from '@/lib/dayjs';

const Attendance = () => {
  const columnsHelper = createColumnHelper();
  const columns = [
    columnsHelper.accessor('employee.name', {
      header: 'Name',
      size: 150,
    }),
    columnsHelper.accessor('employee.department.name', {
      header: 'Department',
      size: 150,
    }),
    columnsHelper.accessor('histories.dateAttendance', {
      header: 'Date',
      size: 150,
      cell: info => dayjs.utc(info.getValue()).tz().format('YYYY-MM-DD'),
    }),
    columnsHelper.accessor('checkIn', {
      header: 'Check In',
      size: 150,
      cell: info => {
        return info.getValue() ? dayjs.utc(info.getValue()).tz().format('HH:mm') : '-';
      },
    }),
    columnsHelper.accessor('checkOut', {
      header: 'Check Out',
      size: 150,
      cell: info => {
        return info.getValue() ? dayjs.utc(info.getValue()).tz().format('HH:mm') : 'Not Yet';
      },
    }),
  ];

  return (
    <AuthGuard requiredRoles={['admin', 'employee']}>
      <BreadcrumbNav />
      <Card>
        <CardHeader>
          <CardTitle className="text-gray-800">Attendances</CardTitle>
          <CardDescription>Manage attendances</CardDescription>
        </CardHeader>
        <CardContent>
          <DataTable
            columns={columns}
            searchQuery={useSearchAttendancesQuery}
            removeMutation={useRemoveAttendanceMutation}
            entityName="attendance"
            allowCreate={false}
            allowUpdate={false}
          />
        </CardContent>
      </Card>
    </AuthGuard>
  );
};
export default Attendance;
