import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import './zakat.scss'
const Zakat = () => {
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

export default Zakat