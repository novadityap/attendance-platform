'use client';

import Link from 'next/link';
import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from '@/components/shadcn/avatar';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/shadcn/dropdown-menu';
import { TbApps, TbLogout } from 'react-icons/tb';
import { cn } from '@/lib/utils';
import { useSelector } from 'react-redux';
import useSignout from '@/hooks/useSignout';

const UserDropdown = ({ className }) => {
  const { currentEmployee } = useSelector(state => state.auth);
  const { handleSignout } = useSignout();

  const menuItems = [
    {
      name: 'Dashboard',
      icon: TbApps,
      link:
        currentEmployee?.role === 'admin' ? '/dashboard' : '/dashboard/checkin',
    },
    { name: 'Sign Out', icon: TbLogout, action: handleSignout },
  ];

  return (
    <DropdownMenu>
      <DropdownMenuTrigger className={cn('outline-none', className)}>
        <Avatar className="cursor-pointer">
          <AvatarImage src={currentEmployee?.avatar} alt="User Avatar" />
          <AvatarFallback>
            {currentEmployee?.name.charAt(0).toUpperCase()}
          </AvatarFallback>
        </Avatar>
      </DropdownMenuTrigger>
      <DropdownMenuContent className="mr-4 w-40">
        <DropdownMenuLabel>My Account</DropdownMenuLabel>
        <DropdownMenuSeparator />
        {menuItems.map(({ name, icon: Icon, link, action }, index) =>
          link ? (
            <Link key={index} href={link}>
              <DropdownMenuItem className="cursor-pointer hover:focus:bg-gray-200">
                <Icon className="mr-2 size-5" />
                {name}
              </DropdownMenuItem>
            </Link>
          ) : (
            <DropdownMenuItem
              key={index}
              onClick={action}
              className="cursor-pointer hover:focus:bg-gray-200"
            >
              <Icon className="mr-2 size-5" />
              {name}
            </DropdownMenuItem>
          )
        )}
      </DropdownMenuContent>
    </DropdownMenu>
  );
};

export default UserDropdown;
