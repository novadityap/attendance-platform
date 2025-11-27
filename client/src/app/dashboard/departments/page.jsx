'use client';

import DataTable from '@/components/ui/DataTable';
import { createColumnHelper } from '@tanstack/react-table';
import {
  useSearchDepartmentsQuery,
  useRemoveDepartmentMutation,
} from '@/services/departmentApi.js';
import DepartmentForm from '@/components/ui/DepartmentForm.jsx';
import BreadcrumbNav from '@/components/ui/BreadcrumbNav';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/shadcn/card';
import AuthGuard from '@/components/auth/AuthGuard';

const Department = () => {
  const columnsHelper = createColumnHelper();
  const columns = [
    columnsHelper.accessor('name', {
      header: 'Name',
      size: 150,
    }),
    columnsHelper.accessor('minCheckInTime', {
      header: 'Check-In From',
      size: 150,
    }),
    columnsHelper.accessor('minCheckOutTime', {
      header: 'Check-Out From',
      size: 150,
    }),
    columnsHelper.accessor('maxCheckInTime', {
      header: 'Check-In Until',
      size: 150,
    }),
    columnsHelper.accessor('maxCheckOutTime', {
      header: 'Check-Out Until',
      size: 150,
    }),
  ];

  return (
    <AuthGuard requiredRoles={['admin']}>
      <BreadcrumbNav />
      <Card>
        <CardHeader>
          <CardTitle className="text-gray-800">Departments</CardTitle>
          <CardDescription>Manage departments</CardDescription>
        </CardHeader>
        <CardContent>
          <DataTable
            columns={columns}
            searchQuery={useSearchDepartmentsQuery}
            removeMutation={useRemoveDepartmentMutation}
            FormComponent={DepartmentForm}
            entityName="department"
          />
        </CardContent>
      </Card>
    </AuthGuard>
  );
};
export default Department;
