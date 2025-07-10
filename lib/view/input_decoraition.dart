import 'package:charity_project/app_colors.dart';
import 'package:flutter/material.dart';
import 'package:flutter/material.dart';
 // لو عندك ستايل نصوص

class AppInputDecoration {
  static InputDecoration get defaultDecoration => InputDecoration(
    
    labelStyle: TextStyle(color: AppColors.primary),
    
    suffixStyle: TextStyle(color: AppColors.primary),
    filled: true,
    fillColor: const Color.fromARGB(255, 248, 247, 245),
    border: OutlineInputBorder(
      
      borderRadius: BorderRadius.circular(12),
    ),
    enabledBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: BorderSide(color: AppColors.primary, width: 1.5),
    ),
    focusedBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: BorderSide(color: AppColors.primary, width: 2),
    ),
    errorBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: BorderSide(color: Colors.red, width: 1.5),
    ),
    focusedErrorBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: BorderSide(color: Colors.red, width: 2),
    ),
  );
}
