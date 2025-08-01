import React, { useState } from 'react';
import Sidebar from '../../components/sidebar/Sidebar';
import Navbar from '../../components/navbar/Navbar';
import Filter from '../../components/filters/Filter';
import InfoBox from '../../components/infoBox/InfoBox';
import './Volunteer_request.scss';
// import backgroundImage from './assets/5.png';

const VolunteerRequest = () => {
  const [activeFilter, setActiveFilter] = useState('all');

  const volunteersData = [
    { id: 1, firstName: 'محمد', lastName: 'علي', age: 25, status: 'نشط' },
    { id: 2, firstName: 'أحمد', lastName: 'خالد', age: 30, status: 'قيد المراجعة' },
    { id: 3, firstName: 'ليلى', lastName: 'محمود', age: 22, status: 'غير نشط' },
    { id: 4, firstName: 'نور', lastName: 'حسن', age: 28, status: 'نشط' },
  ];

  const filterButtons = [
    {
      text: "الكل",
      value: "all",
      color: "primary",
      hoverColor: "#e3f2fd",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('all')
    },
    {
      text: "مقبول",
      value: "Sorted",
      color: "secondary",
      hoverColor: "#f3e5f5",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('Sorted')
    },
    {
      text: "قيد الانتظار",
      value: "Pending",
      color: "success",
      hoverColor: "#e8f5e9",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('Pending')
    },
    {
      text: "غير مقروء",
      value: "Unread",
      color: "primary",
      hoverColor: "#e3f2fd",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('Unread')
    }
  ];

  // تصفية المتطوعين حسب الفلتر النشط
  const filteredVolunteers = volunteersData.filter(volunteer => {
    if (activeFilter === 'all') return true;
    if (activeFilter === 'Sorted') return volunteer.status === 'نشط';
    if (activeFilter === 'Pending') return volunteer.status === 'قيد المراجعة';
    if (activeFilter === 'Unread') return volunteer.status === 'غير نشط';
    return true;
  });

  return (
    <div className="volunteer_request"   >
      <Sidebar />
      <div className="volunteer_requestContainer">
        <Navbar />
        <Filter 
          buttons={filterButtons} 
          activeFilter={activeFilter}
          setActiveFilter={setActiveFilter}
        />
          
           <InfoBox volunteers={volunteersData} />
           
          
        </div>
      </div>
  );
};

export default VolunteerRequest;