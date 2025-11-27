'use client';

import {
  Card,
  CardHeader,
  CardContent,
  CardTitle,
  CardDescription,
} from '@/components/shadcn/card';
import { Skeleton } from '@/components/shadcn/skeleton';
import { useShowDashboardQuery } from '@/services/dashboardApi';
import BreadcrumbNav from '@/components/ui/BreadcrumbNav';
import Link from 'next/link';
import { cn } from '@/lib/utils';
import dayjs from '@/lib/dayjs';
import AuthGuard from '@/components/auth/AuthGuard';
import {
  TbLogin,
  TbLogout,
  TbCalendar,
  TbClock,
  TbBuildingCommunity,
  TbClipboardCheck,
  TbUsers,
  TbShieldCheck,
  TbTrendingUp,
  TbArrowRight,
  TbUserCircle,
} from 'react-icons/tb';
import { useSelector } from 'react-redux';

const StatCard = ({ title, value, icon: Icon, gradient, iconColor, link }) => (
  <Card className="relative overflow-hidden hover:shadow-2xl transition-all duration-300 border-0 group">
    <div
      className={cn(
        'absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity',
        gradient
      )}
    />
    <CardHeader className="relative pb-2">
      <div className="flex items-start justify-between">
        <div className="flex-1">
          <CardDescription className="text-sm font-medium text-gray-600 mb-1">
            {title}
          </CardDescription>
          <CardTitle
            className={cn(
              'text-3xl font-bold',
              value == null ? 'text-gray-400' : 'text-gray-900'
            )}
          >
            {value ?? '—'}
          </CardTitle>
        </div>
        <div
          className={cn(
            'w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform',
            gradient
          )}
        >
          <Icon className={cn('size-6', iconColor)} />
        </div>
      </div>
    </CardHeader>
    <CardContent className="relative pt-2">
      <div className="flex items-center gap-1 text-xs text-gray-500">
        <TbTrendingUp className="w-3.5 h-3.5 text-green-500" />
        <Link href={link} className="font-medium">
          View details
        </Link>
      </div>
    </CardContent>
  </Card>
);

const RecentItemsCard = ({ title, items, link }) => (
  <Card className="hover:shadow-2xl transition-all duration-300 border-0 overflow-hidden">
    <CardHeader className="border-b bg-gradient-to-r from-slate-50 via-blue-50 to-indigo-50 pb-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="size-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
            <TbClock className="size-5 text-white" />
          </div>
          <div>
            <CardTitle className="text-xl font-bold text-gray-900">
              {title}
            </CardTitle>
            <p className="text-xs text-gray-500 mt-0.5">
              Latest activity records
            </p>
          </div>
        </div>
        {items?.length > 0 && (
          <Link
            href={link}
            className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-sm font-semibold text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 shadow-sm hover:shadow-md group"
          >
            View All
            <TbArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
          </Link>
        )}
      </div>
    </CardHeader>
    <CardContent className="p-0">
      {items?.length ? (
        <div className="divide-y divide-gray-100">
          {items.map(attendance => (
            <Link
              key={attendance.id}
              href={`/dashboard/attendances`}
              className="block p-5 hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/30 transition-all duration-200 group"
            >
              <div className="flex items-start justify-between gap-4">
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-3 mb-3">
                    <div className="relative">
                      <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-base shadow-lg ring-2 ring-white">
                        {attendance.employee.name.charAt(0).toUpperCase()}
                      </div>
                      {!attendance.checkOut && (
                        <div className="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white animate-pulse" />
                      )}
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="font-bold text-gray-900 group-hover:text-blue-700 transition-colors truncate text-base">
                        {attendance.employee.name}
                      </p>
                      <div className="flex items-center gap-1.5 text-xs text-gray-500 mt-0.5">
                        <TbCalendar className="size-3.5" />
                        <span className="font-medium">
                          {dayjs.utc(attendance.createdAt).tz().format('DD MMM YYYY')}
                        </span>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center gap-6 ml-0 bg-gray-50 rounded-lg p-3">
                    <div className="flex items-center gap-2.5 flex-1">
                      <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center shadow-md">
                        <TbLogin className="size-5 text-white" />
                      </div>
                      <div>
                        <p className="text-xs font-medium text-gray-500 mb-0.5">
                          Check In
                        </p>
                        <p className="text-base font-bold text-gray-900">
                          {dayjs.utc(attendance.checkIn).tz().format('HH:mm')}
                        </p>
                      </div>
                    </div>

                    <div className="w-px h-10 bg-gray-200" />

                    <div className="flex items-center gap-2.5 flex-1">
                      <div
                        className={cn(
                          'w-9 h-9 rounded-lg flex items-center justify-center shadow-md',
                          attendance.checkOut
                            ? 'bg-gradient-to-br from-red-400 to-rose-500'
                            : 'bg-gray-200'
                        )}
                      >
                        <TbLogout
                          className={cn(
                            'size-5',
                            attendance.checkOut ? 'text-white' : 'text-gray-400'
                          )}
                        />
                      </div>
                      <div>
                        <p className="text-xs font-medium text-gray-500 mb-0.5">
                          Check Out
                        </p>
                        <p
                          className={cn(
                            'text-base font-bold',
                            attendance.checkOut
                              ? 'text-gray-900'
                              : 'text-gray-400'
                          )}
                        >
                          {attendance.checkOut
                            ? dayjs.utc(attendance.checkOut).tz().format('HH:mm')
                            : 'Not yet'}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                {!attendance.checkOut && (
                  <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-yellow-400 to-amber-500 text-white shadow-md">
                    <span className="w-1.5 h-1.5 bg-white rounded-full animate-pulse" />
                    Active
                  </span>
                )}
              </div>
            </Link>
          ))}
        </div>
      ) : (
        <div className="p-16 text-center">
          <div className="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-inner">
            <TbClock className="size-10 text-gray-400" />
          </div>
          <p className="text-gray-900 font-bold text-lg mb-1">
            No recent attendances
          </p>
          <p className="text-sm text-gray-500">
            Attendance records will appear here
          </p>
        </div>
      )}
    </CardContent>
  </Card>
);

const Dashboard = () => {
  const { currentEmployee } = useSelector(state => state.auth);
  const { data, isLoading } = useShowDashboardQuery();

  const stats = [
    {
      title: 'Total Departments',
      value: data?.data?.totalDepartments,
      icon: TbBuildingCommunity,
      gradient: 'bg-gradient-to-br from-blue-500 to-cyan-500',
      iconColor: 'text-white',
      link: '/dashboard/departments',
    },
    {
      title: 'Total Attendances',
      value: data?.data?.totalAttendances,
      icon: TbClipboardCheck,
      gradient: 'bg-gradient-to-br from-emerald-500 to-teal-500',
      iconColor: 'text-white',
      link: '/dashboard/attendances',
    },
    {
      title: 'Total Employees',
      value: data?.data?.totalEmployees,
      icon: TbUsers,
      gradient: 'bg-gradient-to-br from-violet-500 to-purple-500',
      iconColor: 'text-white',
      link: '/dashboard/employees',
    },
    {
      title: 'Total Roles',
      value: data?.data?.totalRoles,
      icon: TbShieldCheck,
      gradient: 'bg-gradient-to-br from-amber-500 to-orange-500',
      iconColor: 'text-white',
      link: '/dashboard/roles',
    },
  ];

  return (
    <AuthGuard requiredRoles={['admin']}>
      <div className="space-y-6">
        <BreadcrumbNav />

        <div className="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 shadow-2xl">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-bold text-white mb-2">
                Welcome back,{' '}
                {currentEmployee?.name?.charAt(0).toUpperCase() +
                  currentEmployee?.name?.slice(1)}
                !
              </h1>
              <p className="text-blue-100 text-base">
                Here's what's happening with your organization today
              </p>
            </div>
            <div className="hidden md:block">
              <div className="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <TbUserCircle className="size-10 text-white" />
              </div>
            </div>
          </div>
        </div>

        <Card className="border-0 shadow-lg">
          <CardHeader className="pb-6">
            <CardTitle className="text-2xl font-bold text-gray-900">
              Overview Statistics
            </CardTitle>
            <CardDescription className="text-gray-600">
              Key metrics and performance indicators
            </CardDescription>
          </CardHeader>
          <CardContent>
            {isLoading ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {stats.map((_, index) => (
                  <Skeleton key={index} className="h-32 rounded-xl" />
                ))}
              </div>
            ) : (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {stats.map((stat, index) => (
                  <StatCard
                    key={index}
                    title={stat.title}
                    value={stat.value}
                    icon={stat.icon}
                    gradient={stat.gradient}
                    iconColor={stat.iconColor}
                    link={stat.link}
                  />
                ))}
              </div>
            )}
          </CardContent>
        </Card>

        <div className="grid grid-cols-1 gap-6">
          {isLoading ? (
            <Skeleton className="h-96 rounded-xl" />
          ) : (
            <RecentItemsCard
              title="Recent Attendances"
              items={data?.data?.recentAttendances}
              link="/dashboard/attendances"
            />
          )}
        </div>
      </div>
    </AuthGuard>
  );
};

export default Dashboard;
