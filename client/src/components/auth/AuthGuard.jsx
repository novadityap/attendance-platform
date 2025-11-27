'use client'

import { useSelector } from 'react-redux';
import {useEffect} from 'react';
import { useRouter } from 'next/navigation';

const AuthGuard = ({ requiredRoles, children }) => {
  const router = useRouter();
  const { token, currentEmployee } = useSelector(state => state.auth);

  useEffect(() => {
    if (!token) {
      router.replace('/');
    } else if (!requiredRoles.includes(currentEmployee?.role)) {
      router.replace('/unauthorized');
    }
  }, [token, currentEmployee, requiredRoles, router]);

  return <>{children}</>;
};

export default AuthGuard;
