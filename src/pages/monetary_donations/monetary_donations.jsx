import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import './monetary_donations.scss'

const monetary_donations = () => {
  return (
    <div>
        <Sidebar />
       <div className="volunteerContainer">
        <Navbar />
        <Filter/>
      </div>
    </div>
  )
}

export default monetary_donations