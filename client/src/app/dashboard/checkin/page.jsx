'use client';

import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardDescription,
} from '@/components/shadcn/card';
import { Button } from '@/components/shadcn/button';
import { toast } from 'react-hot-toast';
import {
  useCheckInAttendanceMutation,
  useTodayAttendanceQuery,
} from '@/services/attendanceApi.js';
import { useShowDepartmentQuery } from '@/services/departmentApi';
import { TbLogin, TbUser, TbBuilding, TbClock } from 'react-icons/tb';
import AuthGuard from '@/components/auth/AuthGuard';
import BreadcrumbNav from '@/components/ui/BreadcrumbNav';
import { useSelector } from 'react-redux';
import { Skeleton } from '@/components/shadcn/skeleton';

const CheckInSkeleton = () => (
  <Card className="max-w-md mx-auto">
    <CardHeader>
      <Skeleton className="h-6 w-32 mb-2" />
      <Skeleton className="h-4 w-56" />
    </CardHeader>
    <CardContent className="space-y-8">
      <div className="p-5 rounded-xl border space-y-4">
        <div className="flex items-center gap-3">
          <Skeleton className="size-10 rounded-xl" />
          <div className="space-y-2">
            <Skeleton className="h-4 w-40" />
            <Skeleton className="h-3 w-24" />
          </div>
        </div>
        <div className="flex items-center gap-3">
          <Skeleton className="size-10 rounded-xl" />
          <div className="space-y-2">
            <Skeleton className="h-4 w-32" />
            <Skeleton className="h-3 w-20" />
          </div>
        </div>
      </div>
      <div className="p-5 rounded-xl border space-y-4">
        <Skeleton className="h-4 w-32" />
        <div className="flex items-center justify-between">
          <Skeleton className="h-4 w-40" />
          <Skeleton className="h-4 w-20" />
        </div>
        <div className="flex items-center justify-between">
          <Skeleton className="h-4 w-40" />
          <Skeleton className="h-4 w-20" />
        </div>
      </div>
      <div className="flex justify-center">
        <Skeleton className="h-12 w-44 rounded-lg" />
      </div>
    </CardContent>
  </Card>
);

const CheckIn = () => {
  const { currentEmployee } = useSelector(state => state.auth);
  const { data: todayAttendance, isLoading: isTodayAttendanceLoading } =
    useTodayAttendanceQuery(undefined, {
      refetchOnMountOrArgChange: true,
    });
  const { data: department, isLoading: isDepartmentLoading } =
    useShowDepartmentQuery(currentEmployee?.departmentId);
  const [checkIn, { isLoading }] = useCheckInAttendanceMutation();

  const handleCheckIn = () => {
    checkIn()
      .unwrap()
      .then(res => toast.success(res.message))
      .catch(err => toast.error(err?.message || 'Failed to check-in'));
  };

  if (isDepartmentLoading || isTodayAttendanceLoading) {
    return <CheckInSkeleton />;
  }

  return (
    <AuthGuard requiredRoles={['admin', 'employee']}>
      <BreadcrumbNav />
      <Card>
        <CardHeader>
          <CardTitle className="text-gray-800">Check In</CardTitle>
          <CardDescription>Check in for today</CardDescription>
        </CardHeader>
        <CardContent className="space-y-10">
          <div className="space-y-5 bg-gradient-to-br from-blue-50/40 via-white to-blue-100/30 p-5 rounded-xl border border-blue-100 shadow-sm">
            <div className="flex items-center gap-3">
              <div className="size-10 rounded-xl bg-emerald-200 text-emerald-700 flex items-center justify-center shadow-sm">
                <TbUser className="size-6" />
              </div>
              <div>
                <p className="text-base font-bold text-gray-900 tracking-wide">
                  {currentEmployee?.name}
                </p>
                <p className="text-xs text-gray-600">Employee</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <div className="size-10 rounded-xl bg-indigo-200 text-indigo-700 flex items-center justify-center shadow-sm">
                <TbBuilding className="size-6" />
              </div>
              <div>
                <p className="text-sm font-semibold text-gray-900">
                  Department
                </p>
                <p className="text-xs text-indigo-700 font-medium mt-0.5">
                  {department?.data?.name}
                </p>
              </div>
            </div>
          </div>
          <div className="rounded-2xl p-6 border bg-gradient-to-br from-white via-gray-50 to-gray-100 shadow-inner space-y-6 transition-all">
            <h3 className="text-sm font-semibold text-gray-700 flex items-center gap-2">
              <TbClock className="size-5 text-amber-500" />
              Working Hours
            </h3>
            <div className="flex items-center justify-between bg-white/70 p-4 rounded-xl border shadow-sm hover:shadow-md transition-all">
              <div className="flex items-center gap-3">
                <span className="size-9 flex items-center justify-center rounded-xl bg-amber-200 text-amber-700 shadow-sm">
                  <TbClock className="size-5" />
                </span>
                <p className="text-sm font-semibold text-gray-800">
                  Min Check In
                </p>
              </div>
              <span className="text-sm font-bold text-gray-900">
                {department?.data?.minCheckInTime}
              </span>
            </div>
            <div className="flex items-center justify-between bg-white/70 p-4 rounded-xl border shadow-sm hover:shadow-md transition-all">
              <div className="flex items-center gap-3">
                <span className="size-9 flex items-center justify-center rounded-xl bg-red-200 text-red-700 shadow-sm">
                  <TbClock className="size-5" />
                </span>
                <p className="text-sm font-semibold text-gray-800">
                  Max Check In
                </p>
              </div>
              <span className="text-sm font-bold text-gray-900">
                {department?.data?.maxCheckInTime}
              </span>
            </div>
          </div>
          <div className="flex justify-center">
            {todayAttendance?.data?.checkIn ? (
              <Button
                size="lg"
                disabled
                className="h-12 text-base font-semibold px-10 rounded-xl shadow-sm flex items-center gap-2 bg-gradient-to-r from-green-400 to-green-500 text-white cursor-not-allowed"
              >
                <TbClock className="size-5 text-white" />
                Checked In
              </Button>
            ) : (
              <Button
                onClick={handleCheckIn}
                disabled={isLoading}
                size="lg"
                className="h-12 cursor-pointer text-base font-semibold px-10 rounded-xl shadow-sm flex items-center gap-2 transition-all  hover:shadow-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white disabled:opacity-70"
              >
                {isLoading ? (
                  <>
                    <TbClock className="size-5 animate-spin text-white/80" />
                    Processing...
                  </>
                ) : (
                  <>
                    <TbLogin className="size-5 text-white" />
                    Check In Now
                  </>
                )}
              </Button>
            )}
          </div>
        </CardContent>
      </Card>
    </AuthGuard>
  );
};

export default CheckIn;
