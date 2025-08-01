import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import './cahriatable_donations.scss'

const Charitable_donations = () => {
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

export default Charitable_donations