import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import './campaignArchive.scss'
import { useState } from 'react'

const CampaignArchive = () => {
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
      value: "new",
      color: "secondary",
      hoverColor: "#f3e5f5",
      activeTextColor: "#ffffff",
      onClick: () => {
        setActiveFilter('new');
      }
    },
    {
      text: "Build",
      value: "popular",
      color: "success",
      hoverColor: "#e8f5e9",
      activeTextColor: "#ffffff",
      onClick: () => {
        setActiveFilter('popular');
      }
    }
  ];

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
        
      </div>
    </div>
  );
};

export default CampaignArchive