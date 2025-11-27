'use client';

import { Button } from '@/components/shadcn/button';
import { Input } from '@/components/shadcn/input';
import useFormHandler from '@/hooks/useFormHandler';
import {
  Form,
  FormField,
  FormLabel,
  FormMessage,
  FormItem,
  FormControl,
} from '@/components/shadcn/form';
import { TbLoader } from 'react-icons/tb';
import {
  useShowDepartmentQuery,
  useCreateDepartmentMutation,
  useUpdateDepartmentMutation,
} from '@/services/departmentApi';
import { useEffect } from 'react';
import { Skeleton } from '@/components/shadcn/skeleton';
import { toast } from 'react-hot-toast';

const DepartmentFormSkeleton = () => (
  <div className="space-y-4">
    <div className="space-y-2">
      <Skeleton className="h-4 w-20" />
      <Skeleton className="h-10 w-full rounded-md" />
    </div>

    <div className="flex justify-end gap-x-2">
      <Skeleton className="h-10 w-24 rounded-md" />
      <Skeleton className="h-10 w-24 rounded-md" />
    </div>
  </div>
);

const DepartmentForm = ({ onSuccess, onClose, isUpdate, id }) => {
  const { data: department, isLoading: isDepartmentLoading } =
    useShowDepartmentQuery(id, {
      skip: !isUpdate || !id,
    });
  const { form, handleSubmit, isLoading } = useFormHandler({
    isUpdate,
    mutation: isUpdate
      ? useUpdateDepartmentMutation
      : useCreateDepartmentMutation,
    defaultValues: {
      name: '',
      minCheckInTime: '',
      minCheckOutTime: '',
      maxCheckInTime: '',
      maxCheckOutTime: '',
    },
    onSuccess: result => {
      onSuccess();
      toast.success(result.message);
    },
    onError: e => toast.error(e.message),
    ...(isUpdate && { params: [{ name: 'departmentId', value: id }] }),
  });

  useEffect(() => {
    if (isUpdate && department?.data) {
      form.reset({
        name: department.data.name,
        minCheckInTime: department.data.minCheckInTime,
        minCheckOutTime: department.data.minCheckOutTime,
        maxCheckInTime: department.data.maxCheckInTime,
        maxCheckOutTime: department.data.maxCheckOutTime,
      });
    }
  }, [department]);

  if (isDepartmentLoading) return <DepartmentFormSkeleton />;

  return (
    <Form {...form}>
      <form className="space-y-4" onSubmit={handleSubmit}>
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
          name="minCheckInTime"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Check-In Start Time</FormLabel>
              <FormControl>
                <Input type="time" {...field} />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />
        <FormField
          control={form.control}
          name="maxCheckInTime"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Check-In End Time</FormLabel>
              <FormControl>
                <Input type="time" {...field} />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />
        <FormField
          control={form.control}
          name="minCheckOutTime"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Check-Out Start Time</FormLabel>
              <FormControl>
                <Input type="time" {...field} />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />
        <FormField
          control={form.control}
          name="maxCheckOutTime"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Check-Out End Time</FormLabel>
              <FormControl>
                <Input type="time" {...field} />
              </FormControl>
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

export default DepartmentForm;
