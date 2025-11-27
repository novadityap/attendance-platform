'use client';

const PublicLayout = ({ children }) => {
  return <div className="min-h-screen container mx-auto px-5 lg:px-10 xl:px-20 flex items-center justify-center">
    {children}
  </div>;
};

export default PublicLayout;