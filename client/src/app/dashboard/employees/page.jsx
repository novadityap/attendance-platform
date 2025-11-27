'use client';

import DataTable from '@/components/ui/DataTable';
import { createColumnHelper } from '@tanstack/react-table';
import {
  useSearchEmployeesQuery,
  useRemoveEmployeeMutation,
} from '@/services/employeeApi.js';
import EmployeeForm from '@/components/ui/EmployeeForm.jsx';
import BreadcrumbNav from '@/components/ui/BreadcrumbNav';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/shadcn/card';
import AuthGuard from '@/components/auth/AuthGuard';

const Employee = () => {
  const columnsHelper = createColumnHelper();
  const columns = [
    columnsHelper.accessor('name', {
      header: 'Name',
      size: 150,
    }),
    columnsHelper.accessor('email', {
      header: 'Email',
      size: 150,
    }),
    columnsHelper.accessor('department.name', {
      header: 'Department',
      size: 200,
    }),
  ];

  return (
    <AuthGuard requiredRoles={['admin']}>
      <BreadcrumbNav />
      <Card>
        <CardHeader>
          <CardTitle className="text-gray-800">Employees</CardTitle>
          <CardDescription>Manage employees</CardDescription>
        </CardHeader>
        <CardContent>
          <DataTable
            columns={columns}
            searchQuery={useSearchEmployeesQuery}
            removeMutation={useRemoveEmployeeMutation}
            FormComponent={EmployeeForm}
            entityName="employee"
          />
        </CardContent>
      </Card>
    </AuthGuard>
  );
};
export default Employee;
