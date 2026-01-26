'use client';

import { Button } from '@/components/shadcn/button';
import { Input } from '@/components/shadcn/input';
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '@/components/shadcn/avatar';
import useFormHandler from '@/hooks/useFormHandler';
import {
  Select,
  SelectContent,
  SelectTrigger,
  SelectValue,
  SelectItem,
} from '@/components/shadcn/select';
import {
  Form,
  FormField,
  FormLabel,
  FormMessage,
  FormItem,
  FormControl,
} from '@/components/shadcn/form';
import { TbLoader } from 'react-icons/tb';
import { useListDepartmentsQuery } from '@/services/departmentApi';
import { useListRolesQuery } from '@/services/roleApi';
import {
  useShowEmployeeQuery,
  useCreateEmployeeMutation,
  useUpdateEmployeeMutation,
} from '@/services/employeeApi';
import { useEffect } from 'react';
import { Skeleton } from '@/components/shadcn/skeleton';
import { toast } from 'react-hot-toast';

const EmployeeFormSkeleton = ({ isUpdate }) => (
  <div className="space-y-4">
    {isUpdate && (
      <div className="flex justify-center">
        <Skeleton className="h-32 w-32 rounded-full" />
      </div>
    )}
    <Skeleton className="h-4 w-20" />
    <Skeleton className="h-10 w-full" />
    <Skeleton className="h-4 w-20" />
    <Skeleton className="h-10 w-full" />
    <Skeleton className="h-4 w-20" />
    <Skeleton className="h-10 w-full" />
    <Skeleton className="h-4 w-20" />
    <Skeleton className="h-10 w-full" />
    <Skeleton className="h-4 w-20" />
    <Skeleton className="h-10 w-full" />
    <div className="flex justify-end gap-2">
      <Skeleton className="h-10 w-24 rounded-md" />
      <Skeleton className="h-10 w-24 rounded-md" />
    </div>
  </div>
);

const EmployeeForm = ({ id, onSuccess, onClose, isUpdate }) => {
  const { data: employee, isLoading: isEmployeeLoading } = useShowEmployeeQuery(id, {
    skip: !isUpdate || !id,
  });
  const { data: roles, isLoading: isRolesLoading } = useListRolesQuery();
  const { data: departments, isLoading: isDepartmentsLoading } = useListDepartmentsQuery();
  const { form, handleSubmit, isLoading } = useFormHandler({
    isUpdate,
    mutation: isUpdate ? useUpdateEmployeeMutation : useCreateEmployeeMutation,
    defaultValues: {
      name: '',
      email: '',
      password: '',
      roleId: '',
      departmentId: '',
    },
    ...(isUpdate && {
      params: [{ name: 'employeeId', value: id }],
      file: { fieldName: 'avatar', isMultiple: false, method: 'PUT' },
    }),
    onSuccess: result => {
      onSuccess();
      toast.success(result.message);
    },
    onError: e => toast.error(e.message),
  });

  useEffect(() => {
    if (isUpdate && employee?.data && roles?.data?.length > 0 && departments?.data?.length > 0) {
      form.reset({
        name: employee.data.name,
        email: employee.data.email,
        roleId: employee.data.roleId,
        departmentId: employee.data.departmentId,
        password: '',
      });
    }
  }, [employee, roles, departments]);

  if (isEmployeeLoading || isRolesLoading || isDepartmentsLoading) {
    return <EmployeeFormSkeleton isUpdate={isUpdate} />;
  }

  return (
    <Form {...form}>
      <form className="space-y-4" onSubmit={handleSubmit}>
        {isUpdate && (
          <>
            <div className="flex justify-center">
              <Avatar className="size-32">
                <AvatarImage
                  src={employee?.data?.avatar}
                  fallback={
                    <AvatarFallback>
                      {employee?.data?.name.charAt(0).toUpperCase()}
                    </AvatarFallback>
                  }
                />
              </Avatar>
            </div>
            <FormField
              control={form.control}
              name="avatar"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Avatar</FormLabel>
                  <FormControl>
                    <Input
                      type="file"
                      accept="image/*"
                      onChange={e => field.onChange(e.target.files[0])}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
          </>
        )}
        <FormField
          control={form.control}
          name="name"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Name</FormLabel>
              <FormControl>
                <Input {...field} />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />
        <FormField
          control={form.control}
          name="email"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Email</FormLabel>
              <FormControl>
                <Input type="email" {...field} />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />
        <FormField
          control={form.control}
          name="password"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Password</FormLabel>
              <FormControl>
                <Input type="password" {...field} />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />
        <FormField
          control={form.control}
          name="roleId"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Role</FormLabel>
              <Select
                key={field.value}
                value={field.value}
                onValueChange={field.onChange}
              >
                <FormControl>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="Select a role" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  {roles?.data?.map(role => (
                    <SelectItem key={role.id} value={role.id}>
                      {role.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <FormMessage />
            </FormItem>
          )}
        />
        <FormField
          control={form.control}
          name="departmentId"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Department</FormLabel>
              <Select
                key={field.value}
                value={field.value}
                onValueChange={field.onChange}
              >
                <FormControl>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="Select a department" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  {departments?.data?.map(department => (
                    <SelectItem key={department.id} value={department.id}>
                      {department.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <FormMessage />
            </FormItem>
          )}
        />
        <div className="flex justify-end gap-x-2">
          <Button variant="secondary" type="button" onClick={onClose}>
            Cancel
          </Button>
          <Button type="submit" disabled={isLoading}>
            {isLoading ? (
              <>
                <TbLoader className="animate-spin" />
                {isUpdate ? 'Updating..' : 'Creating..'}
              </>
            ) : isUpdate ? (
              'Update'
            ) : (
              'Create'
            )}
          </Button>
        </div>
      </form>
    </Form>
  );
};

export default EmployeeForm;
