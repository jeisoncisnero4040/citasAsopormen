import * as XLSX from 'xlsx';

export class ExcelService {
  static exportToExcel(data, fileName = 'archivo.xlsx') {
    const worksheet = XLSX.utils.json_to_sheet(data);  
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Datos');
    XLSX.writeFile(workbook, fileName);
  }
}
