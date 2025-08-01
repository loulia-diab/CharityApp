import React from 'react'
import "./volunteer.scss"
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import InfoBox from '../../components/infoBox/InfoBox'
import { useState } from 'react';


export const Volunteer = () => {
  const volunteersData = [
    { id: 1, firstName: 'محمد', lastName: 'علي', age: 25, status: 'نشط' },
    { id: 2, firstName: 'أحمد', lastName: 'خالد', age: 30 },
    { id: 3, firstName: 'ليلى', lastName: 'محمود', age: 22, status: 'غير نشط' },
    { id: 4, firstName: 'نور', lastName: 'حسن', age: 28, status: 'نشط' },
  ];

  const [activeFilter, setActiveFilter] = useState('all');
  
    const filterButtons = [
      {
        text: "all",
        value: "all",
        color: "primary",
        hoverColor: "#e3f2fd",
        activeTextColor: "#ffffff",
        onClick: () => {
          setActiveFilter('all');
        }
      },
      {
        text: "Health",
        value: "Health",
        color: "secondary",
        hoverColor: "#f3e5f5",
        activeTextColor: "#ffffff",
        onClick: () => {
          setActiveFilter('Health');
        }
      },
      {
        text: "Build",
        value: "Build",
        color: "success",
        hoverColor: "#e8f5e9",
        activeTextColor: "#ffffff",
        onClick: () => {
          setActiveFilter('Build');
        }
      },
      {
        text: "Education",
        value: "Education",
        color: "primary",
        hoverColor: "#e3f2fd",
        activeTextColor: "#ffffff",
        onClick: () => {
          setActiveFilter('Education');
        }
      }
    ];

  return (
    <div className='volunteer'>
       <Sidebar />
       <div className="volunteerContainer">
        <Navbar />
         <Filter
          buttons={filterButtons}
          activeFilter={activeFilter}
          spacing={3}
          buttonProps={{
            sx: {
              minWidth: '120px',
              fontSize: '0.875rem'
            }
          }}
        />          <InfoBox
  volunteers={volunteersData}
  columns={[
    { field: 'id', headerName: 'رقم المتطوع' },
    { 
      field: 'fullName', 
      headerName: 'الاسم الكامل',
      valueGetter: (v) => `${v.firstName} ${v.lastName}`
    },
    { 
      field: 'status', 
      headerName: 'حالة التطوع',
      valueGetter: (v) => (
        <span style={{ 
          color: v.status === 'نشط' ? 'green' : 
                v.status === 'قيد المراجعة' ? 'orange' : 'red',
          fontWeight: 'bold'
        }}>
          {v.status}
        </span>
      )
    }
  ]}
  title="سجل المتطوعين"  // عنوان مخصص
  titleVariant="h4"      // حجم أكبر للعنوان
  showDetailsButton={true}
/>
            </div>
      </div>
  )
}
export default Volunteer
