import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import EditDataBox from '../../components/editableDataBox/EditDataBox'
import PeopleButton from '../../components/peopleButton/PeopleButton'
import Datatable from '../../components/datatable/Datatable'; // استيراد جدول البيانات
import './campaign_details.scss'
import { useState } from 'react'
import Box from '@mui/material/Box';

const Campaign_details = () => {
  const [activeFilter, setActiveFilter] = useState('all');
  const [activeTable, setActiveTable] = useState(null); // حالة لتتبع الجدول النشط

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
      value: "new",
      color: "secondary",
      hoverColor: "#f3e5f5",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('new')
    },
    {
      text: "Build",
      value: "popular",
      color: "success",
      hoverColor: "#e8f5e9",
      activeTextColor: "#ffffff",
      onClick: () => setActiveFilter('popular')
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

  // بيانات مختلفة لكل جدول
  const tableData = {
    beneficiaries: {
      title: "Beneficiaries",
      columns: [
        { field: 'id', headerName: 'ID', width: 70 },
        { field: 'name', headerName: 'Name', width: 150 },
        { field: 'assistanceType', headerName: 'Assistance Type', width: 150 },
        { field: 'status', headerName: 'Status', width: 120 }
      ],
      rows: [
        { id: 1, name: 'Ahmed Mohamed', assistanceType: 'Financial Aid', status: 'Active' },
        { id: 2, name: 'Fatima Ali', assistanceType: 'Food Supplies', status: 'Pending' }
      ]
    },
    volunteers: {
      title: "Volunteers",
      columns: [
        { field: 'id', headerName: 'ID', width: 70 },
        { field: 'name', headerName: 'Name', width: 150 },
        { field: 'skills', headerName: 'Skills', width: 200 },
        { field: 'hours', headerName: 'Volunteer Hours', width: 120, type: 'number' }
      ],
      rows: [
        { id: 1, name: 'Khalid Hassan', skills: 'Teaching, First Aid', hours: 45 },
        { id: 2, name: 'Layla Ahmed', skills: 'Translation, Cooking', hours: 32 }
      ]
    },
    donators: {
      title: "Donators",
      columns: [
        { field: 'id', headerName: 'ID', width: 70 },
        { field: 'name', headerName: 'Name', width: 150 },
        { field: 'donationType', headerName: 'Donation Type', width: 150 },
        { field: 'amount', headerName: 'Amount (SAR)', width: 120, type: 'number' }
      ],
      rows: [
        { id: 1, name: 'Mohammed Saleh', donationType: 'Zakat', amount: 5000 },
        { id: 2, name: 'Noura Abdullah', donationType: 'General Donation', amount: 2500 }
      ]
    }
  };

  const handleButtonClick = (type) => {
    setActiveTable(type === activeTable ? null : type); // تبديل الجدول عند النقر
  };

  return (
    <div className='campaignArchive'>
      <Sidebar />
      <div className="campaignArchiveContainer">
        <Navbar />
        <Filter 
          buttons={filterButtons}
          activeFilter={activeFilter}
          spacing={2}
          buttonProps={{
            sx: {
              minWidth: '120px',
              fontSize: '0.875rem'
            }
          }}
        />
        <EditDataBox />

        <Box sx={{ 
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          padding: '16px',
          backgroundColor: '#f5f5f5',
          borderRadius: '4px',
          mt: 2,
          gap: 2
        }}>
          <Box>
            <PeopleButton 
              label="Beneficiaries" 
              onClick={() => handleButtonClick('beneficiaries')}
              active={activeTable === 'beneficiaries'}
            />
          </Box>
          
          <Box sx={{ 
            display: 'flex',
            justifyContent: 'center',
            flex: 1
          }}>
            <PeopleButton 
              label="Volunteers" 
              onClick={() => handleButtonClick('volunteers')}
              active={activeTable === 'volunteers'}
            />
          </Box>
          
          <Box>
            <PeopleButton 
              label="Donators" 
              onClick={() => handleButtonClick('donators')}
              active={activeTable === 'donators'}
            />
          </Box>
        </Box>

        {/* عرض جدول البيانات عند اختيار زر */}
        {activeTable && (
          <Box sx={{ mt: 3 }}>
            <Datatable 
              title={tableData[activeTable].title}
              columns={tableData[activeTable].columns}
              rows={tableData[activeTable].rows}
            />
          </Box>
        )}
      </div>
    </div>
  );
};

export default Campaign_details;