import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import './humanterian_cases_details.scss'

const humanterian_cases_details = () => {
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

export default humanterian_cases_details