import React, { useState } from 'react';
import Sidebar from '../../components/sidebar/Sidebar';
import Navbar from '../../components/navbar/Navbar';
import Filter from '../../components/filters/Filter';
import InfoCard from '../../components/infoCard/InfoCard';
import './volunteer_details.scss';
import PeopleButton from '../../components/peopleButton/PeopleButton'
import Box from '@mui/material/Box';



const VolunteerDetails = () => {
  const [activeFilter, setActiveFilter] = useState('all');

  const volunteersData = [
    { id: 1, firstName: 'محمد', lastName: 'علي', age: 25, status: 'نشط' },
    { id: 2, firstName: 'أحمد', lastName: 'خالد', age: 30 },
    { id: 3, firstName: 'ليلى', lastName: 'محمود', age: 22, status: 'غير نشط' },
    { id: 4, firstName: 'نور', lastName: 'حسن', age: 28, status: 'نشط' },
  ];

  const filterButtons = [
    {
      text: "all",
      value: "all",
      color: "primary",
      hoverColor: "#e3f2fd",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('all')
    },
    {
      text: "Health",
      value: "Health",
      color: "secondary",
      hoverColor: "#f3e5f5",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('Health')
    },
    {
      text: "Build",
      value: "Build",
      color: "success",
      hoverColor: "#e8f5e9",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('Build')
    },
    {
      text: "Education",
      value: "Education",
      color: "primary",
      hoverColor: "#e3f2fd",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('Education')
    }
  ];

  const volunteerData = {
    id: 1,
    firstName: 'محمد',
    lastName: 'علي',
    age: 25,
    status: 'نشط'
  };

  return (
    <div className='volunteer_details'>
      <Sidebar />
      <div className="volunteer_detailsContainer">
        <Navbar />
        {/* تمرير الـ props الضرورية لمكون الفلتر */}
        <Filter 
          buttons={filterButtons} 
          activeFilter={activeFilter}
          setActiveFilter={setActiveFilter}
        />
        <InfoCard volunteer={volunteerData} />

        <Box sx={{ 
          display: 'flex',
          justifyContent: 'flex-end',
          padding: '16px',
          backgroundColor: '#f5f5f5',
          borderRadius: '4px',
          mt: 2
        }}>
          <PeopleButton label="Sort" />
        </Box> 
      </div>
    </div>
  );
};

export default VolunteerDetails;