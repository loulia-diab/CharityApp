import React from 'react';
import "./campaign.scss";
import Sidebar from '../../components/sidebar/Sidebar';
import Navbar from '../../components/navbar/Navbar';
import Filter from '../../components/filters/Filter';
import { useState } from 'react';
import CardList from '../../components/cardList/CardList';

const Campaign = () => {



  const cardsData = [
    {
      title: "1",
      description: "Name",
      imageUrl: "https://via.placeholder.com/300",
    },
    {
      title: "2",
      description: "Name",
      imageUrl: "https://via.placeholder.com/300",
    },
    {
      title: "3",
      description: "Name",
      imageUrl: "https://via.placeholder.com/300",
    },
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
    <div className='campaign'>
      <Sidebar />
      <div className="campaignContainer">
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
      <CardList cardsData={cardsData} />
      </div>
    </div>
  );
};

export default Campaign;