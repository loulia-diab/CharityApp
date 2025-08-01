import React, { useState } from 'react';
import Sidebar from '../../components/sidebar/Sidebar';
import Navbar from '../../components/navbar/Navbar';
import Filter from '../../components/filters/Filter';
import "./campaignAdd.scss";
import InputBox from '../../components/inputBox/InputBox';
import Box from '@mui/material/Box';
import SendBottun from '../../components/sendBottun/SendBottun';
import ImageUploadBox from '../../components/imageBox/ImageUploadBox';


const CampaignAdd = () => {
  const [activeFilter, setActiveFilter] = useState('all');
  const [campaignImage, setCampaignImage] = useState(null);


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
  const handleImageUpload = (imageData) => {
    console.log('تم اختيار صورة الحملة:', imageData);
    setCampaignImage(imageData);
  };

  return (
    <div className='campaignAdd'>
      <Sidebar />
      <div className="campaignAddContainer">
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
        
        <InputBox 
  width="500px"
  label="اسم الحملة"
  color="#ffffff"
  boxColor="#d2b48c" // أخضر فاتح
/>
<InputBox 
  width="500px"
  label="campaign name"
  color="#ffffff"
  boxColor="#d2b48c" // أخضر فاتح
/>

       
        <InputBox 
  width="500px"
  label="وصف الحملة"
  color="#ffffff"
  boxColor="#d1bca0ff" 
/>
<InputBox 
  width="500px"
  label="campaign discription"
  color="#ffffff"
  boxColor="#d1bca0ff" 
/>

<InputBox 
  width="200px"
  label="The requested amount"
  color="#ffffff"
  boxColor="#218483ff"
/>
{/* <InputBox 
  width="200px"
  label="The type"
  color="#ffffff"
  boxColor="#218483ff" // أخضر فاتح
/> */}

<ImageUploadBox
          width="100%"
          height="300px"
          label="اسحب وأسقط صورة الحملة هنا"
          uploadLabel="اختر صورة للحملة"
          boxColor="#f8f9fa"
          onImageSelected={handleImageUpload}
          sx={{ mb: 4 }}
        />

<Box sx={{ 
          display: 'flex',
          justifyContent: 'flex-end',
          padding: '16px',
          backgroundColor: '#f5f5f5',
          borderRadius: '4px',
          mt: 2
        }}>
          <SendBottun />
        </Box>     
      </div>
    </div>
  );
};

export default CampaignAdd;