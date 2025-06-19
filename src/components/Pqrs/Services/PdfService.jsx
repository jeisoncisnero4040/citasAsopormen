import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import { DateManager } from "../Utils/DateManger";
import { PdfAnswerClient } from '../Constans/PdfAnswerClient';

export default class PdfService {
  constructor(pqrs, params) {
    this.pqrs = pqrs;
    this.params = params;
    pdfMake.vfs = pdfFonts.pdfMake.vfs;
  }

  openPdfInBrowser() {
    const pdf = pdfMake.createPdf(this.buildPdfDefinition());
    pdf.open();
  }

  async buildPdfFormData() {
    const pdf = pdfMake.createPdf(this.buildPdfDefinition());
    return new Promise((resolve) => {
      pdf.getBlob((blob) => {
        const formData = new FormData();
        formData.append("file", blob, "respuesta.pdf");
        resolve(formData);
      });
    });
  }

  buildPdfDefinition() {
    return {
      pageSize: 'A4',
      pageMargins: [50, 120, 40, 100], 

      header: () => ({
        image: 'header',
        width: 595,
        margin: [0, 0, 0, 0]
      }),

      footer: () => ({
        image: 'footer',
        width: 600,
        margin: [3, 21, 0, 0]
      }),

      content: [
          { text: `Bucaramanga, ${DateManager.formatFechaLarga()}`, style: 'textBody', margin: [10, 0, 0, 20] },
          { text: "Señor (a)", style: 'textBody', margin: [10, 0, 0, 0] },
          { text:this.params.userRegister??'hola1', style: 'textBodyBold', margin: [10, 0, 0, 0]},
          { text:this.params.post,style:'textBody', margin: [10, 0, 0, 0] },
          { text:this.params.areaEps,style:'textBody', margin: [10, 0, 0, 0] },
          { text:this.params.typeUser,style:'textBody', margin: [10, 0, 0, 15] },
          {
            margin: [100, 0, 0, 15],
            alignment: 'right',
            text: [
              { text: 'Referencia: ', style: 'textBodyBold' },
              { text: this.params.motive ?? '', style: 'textBody' }
            ]
          },
          { text:"Respetuoso Saludo,",style:'textBody', margin: [10, 0, 0, 10] },
          { text:PdfAnswerClient.firstParagraph, style:'textBody', margin: [10, 0, 0,10] },
          { text:PdfAnswerClient.secondParagraph, style:'textBody', margin: [10, 0, 0, 10] },
          { text:this.params.answerArea??"", style:'textBody', margin: [10, 0, 0, 10] },
          { text:PdfAnswerClient.thirdParagraph, style:'textBody', margin: [10, 0, 0, 10] },
          { text:PdfAnswerClient.fourthParagraph, style:'textBody', margin: [10, 0, 0, 10] },
          { text:PdfAnswerClient.fifthParagraph, style:'textBodyBold', margin: [10, 0, 0, 10] },
          { text:"Cordialmente,",style:'textBody', margin: [10, 0, 0, 15] },
          { text:"Equipo Experiencia de Servicio al Cliente",style:'textBodyBold', margin: [10, 0, 0, 0] },
          { text:"experienciaservicioalcliente@asopormen.org.co",style:'textLink', margin: [10, 0, 0, 0] },
          { text:"Cel. 3177886912",style:'textBody', margin: [10, 0, 0, 0] },
          { text:"ASOPORMEN",style:'textBody', margin: [10, 0, 0, 0] },
          
      ],

      styles: {
        textBody:{
            fontSize:11,
            fontFamily:'Calibri',
            alignment: 'justify'
        },
        textBodyBold:{
          fontSize:11,
          fontFamily:'Calibri',
          bold:true
        },
          textBodyBoldRigth:{
          fontSize:11,
          fontFamily:'Calibri',
          bold:true,
          alignment: 'justify'
        },
        textBodyRigth:{
            fontSize:11,
            fontFamily:'Calibri',
            alignment: 'justify'
        },
        textLink:{
          fontSize:11,
          fontFamily:'Calibri',
          color:'blue',
          decoration: 'underline' 

        }
      },

      images: {
        header: 'https://res.cloudinary.com/dxalvdckk/image/upload/v1747322718/headerPdf_vpuxnz.jpg',
        footer: 'https://res.cloudinary.com/dxalvdckk/image/upload/v1747324043/footerPdfFinal_ssxbep.png'
      }
    };
  }
}
