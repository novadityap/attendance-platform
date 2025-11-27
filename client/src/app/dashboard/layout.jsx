'use client';

import UserDropdown from '@/components/ui/UserDropdown';
import SidebarMenu from '@/components/ui/SidebarMenu';
import { useState, useEffect, useRef } from 'react';
import { TbMenu2 } from 'react-icons/tb';
import {
  TbHome,
  TbUsers,
  TbBuilding,
  TbUserCog,
  TbChecklist,
  TbCheck,
  TbChecks,
  TbLogout,
} from 'react-icons/tb';
import { cn } from '@/lib/utils';
import Brand from '@/components/ui/Brand';
import { Toaster } from 'react-hot-toast';
import { useSelector } from 'react-redux';
import useSignout from '@/hooks/useSignout';

const Header = ({ toggleSidebar }) => {
  return (
    <header className="bg-white shadow-md p-4 flex justify-between lg:justify-end items-center">
      <TbMenu2
        className="size-5 cursor-pointer lg:hidden"
        onClick={toggleSidebar}
      />
      <UserDropdown />
    </header>
  );
};

const Sidebar = ({ isOpen, ref }) => {
  const { currentEmployee } = useSelector(state => state.auth);
  const { handleSignout } = useSignout();

  const menuItems = [
    ...(currentEmployee?.role === 'admin'
      ? [
          { name: 'Dashboard', icon: TbHome, link: '/dashboard' },
          { name: 'Employees', icon: TbUsers, link: '/dashboard/employees' },
          {
            name: 'Departments',
            icon: TbBuilding,
            link: '/dashboard/departments',
          },
          {
            name: 'Attendances',
            icon: TbChecklist,
            link: '/dashboard/attendances',
          },
          { name: 'Roles', icon: TbUserCog, link: '/dashboard/roles' },
        ]
      : []),
    { name: 'Check In', icon: TbCheck, link: '/dashboard/checkin' },
    { name: 'Check Out', icon: TbChecks, link: '/dashboard/checkout' },
    { name: 'Sign Out', icon: TbLogout, action: handleSignout },
  ];

  return (
    <aside
      ref={ref}
      className={cn(
        'w-64 fixed h-screen inset-y-0 z-30 lg:z-30 bg-black flex flex-col transition duration-500 lg:translate-x-0',
        isOpen ? 'translate-x-0' : '-translate-x-full'
      )}
    >
      <Brand className="border-b border-gray-700 p-4" />
      <SidebarMenu menuItems={menuItems} />
    </aside>
  );
};

const DashboardLayout = ({ children }) => {
  const { currentEmployee } = useSelector(state => state.auth);
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const sidebarRef = useRef(null);
  const handleSidebar = () => {
    setIsSidebarOpen(!isSidebarOpen);
  };

  const handleClickOutside = e => {
    if (sidebarRef.current && !sidebarRef.current.contains(e.target)) {
      setIsSidebarOpen(false);
    }
  };

  useEffect(() => {
    if (isSidebarOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    } else {
      document.removeEventListener('mousedown', handleClickOutside);
    }

    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [isSidebarOpen]);

  return (
    <div className="flex min-h-screen">
      <Toaster position="top-right" />
      <Sidebar
        isOpen={isSidebarOpen}
        ref={sidebarRef}
        currentEmployee={currentEmployee}
      />
      <div className="flex flex-col flex-grow lg:pl-64 overflow-x-hidden">
        <Header toggleSidebar={handleSidebar} />
        <main className="container mx-auto p-5 lg:px-10 xl:px-10">
          {children}
        </main>
      </div>
    </div>
  );
};

export default DashboardLayout;
