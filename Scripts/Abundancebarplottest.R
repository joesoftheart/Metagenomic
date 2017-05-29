library(reshape2)
library(ggplot2)
library(scales)
data=read.table("phylumex2.txt", sep = "\t", header=T)
head(data)
data1=as.data.frame(t(data[,2:ncol(data)]))
colnames(data1)=data$taxon
data2=cbind(taxon=row.names(data1),data1)
data3=melt(data2,id.var="taxon")
data3
cols=c("Proteobacteria"="deepskyblue","Planctomycetes"="chartreuse4","Caldithrix"="cadetblue1","Actinobacteria"="indianred3","Acidobacteria"="burlywood1","Gemmatimonadetes"="blue","Nitrospirae"="aquamarine","Verrucomicrobia"="deeppink","Armatimonadetes"="darkseagreen1","Fusobacteria"="darksalmon","Elusimicrobia"="darkred","Spirochaetes"="darkorchid1","Chlorobi"="darkorange","Lentisphaerae"="darkolivegreen2","Chlamydiae"="darkgreen","Chloroflexi"="cyan","Tenericutes"="darkgray","Firmicutes"="red","Bacteroidetes"="green","Cyanobacteria"="lightpink","Deferribacteres"="lightgoldenrod","Fibrobacteres"="mediumpurple1","Synergistetes"="mediumorchid1","Thermotogae"="lightseagreen","[Thermi]"="lightsalmon","SAR406"="tomato1","other phylum"="coral4","Crenarchaeota"="navyblue","Euryarchaeota"="yellow1","[Parvarchaeota]"="dodgerblue")
gg<-ggplot(data3,aes(x=taxon,y=value, fill=variable))
gg1=gg+geom_bar(stat="identity", colour='black', size=0)+scale_fill_manual(name="",values = cols)+guides(fill=guide_legend(override.aes=list(colour=NA),ncol=5))+xlab("")+ylab("%relative abundance of bacterial phyla")+theme(axis.text.x=element_text(angle=45,hjust=1, size=12)) #No line border
gg2=gg1+theme(legend.direction="horizontal", legend.position="bottom", legend.box="vertical", legend.title.align=0) #+ scale_fill_discrete(name="")
#gg2
ggsave("Fig2_RelativePhylum.tiff", plot=gg2, width=20, height=25, units="cm",dpi=300)
